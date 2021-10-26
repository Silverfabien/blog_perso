<?php

namespace App\Security;

use App\Entity\User\User;
use App\Repository\User\BlockedRepository;
use App\Security\Handler\ConnectionAttemptHandler;
use App\Security\Handler\LastConnectionHandler;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    protected const FIRST_BAN = 5;
    protected const SECOND_BAN = 10;
    protected const THIRD_BAN = 15;
    protected const LAST_BAN = 20;

    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UserPasswordEncoderInterface $passwordEncoder;
    private ConnectionAttemptHandler $connectionAttemptHandler;
    private LastConnectionHandler $lastConnectionHandler;
    private BlockedRepository $blockedRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        ConnectionAttemptHandler $connectionAttemptHandler,
        LastConnectionHandler $lastConnectionHandler,
        BlockedRepository $blockedRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->connectionAttemptHandler = $connectionAttemptHandler;
        $this->lastConnectionHandler = $lastConnectionHandler;
        $this->blockedRepository = $blockedRepository;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request): array
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|mixed|object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            throw new UsernameNotFoundException('Email could not be found.');
        }

        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $findUserBlocked = $this->blockedRepository->findOneBy(['user' => $user, 'blocked' => true], ['id' => 'DESC']);

        if ($this->passwordEncoder->isPasswordValid($user, $credentials['password']) === false) {
            if ($findUserBlocked) {
                // Si unblockedat est supérieur à now
                if ($findUserBlocked->getUnblockedAt() >= new DateTimeImmutable() || !$findUserBlocked->getUnblockedAt()) {
                    throw new CustomUserMessageAuthenticationException($findUserBlocked->getBlockedReason());
                }
            }

            // Si compte non bloqué
            $this->connectionAttemptHandler->connectAttemptHandle($user);

            $connectionAttempt = $user->getConnectionAttempt();

            if ($connectionAttempt === self::FIRST_BAN ||
                $connectionAttempt === self::SECOND_BAN ||
                $connectionAttempt === self::THIRD_BAN ||
                $connectionAttempt === self::LAST_BAN
            ) {
                $userBlocked = $this->blockedRepository->findOneBy(['user' => $user, 'blocked' => true],
                    ['id' => 'DESC']);

                throw new CustomUserMessageAuthenticationException($userBlocked->getBlockedReason());
            }

            return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        }

        if ($this->passwordEncoder->isPasswordValid($user, $credentials['password']) && $findUserBlocked) {
            // Si bloqué définitivement
            if (!$findUserBlocked->getUnblockedAt()) {
                throw new CustomUserMessageAuthenticationException($findUserBlocked->getBlockedReason());
            }

            // Si unblockedat est inférieur à now
            if ($user->getConnectionAttempt() > 0) {
                if ($findUserBlocked->getUnblockedAt() >= new DateTimeImmutable()) {
                    throw new CustomUserMessageAuthenticationException($findUserBlocked->getBlockedReason());
                }
                $this->connectionAttemptHandler->resetConnectionAttemptHandle($user);
                $this->lastConnectionHandler->lastConnection($user);

                return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
            }

            // Si unblockedat est supérieur à now
            throw new CustomUserMessageAuthenticationException($findUserBlocked->getBlockedReason());
        }

        $this->lastConnectionHandler->lastConnection($user);
        $this->connectionAttemptHandler->resetConnectionAttemptHandle($user);

        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('default'));
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
