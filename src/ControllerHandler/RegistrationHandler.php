<?php

namespace App\ControllerHandler;

use App\Entity\User\User;
use App\Repository\User\RankRepository;
use App\Repository\User\UserPictureRepository;
use App\Repository\User\UserRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * Class RegistrationHandler
 *
 * Gestion des données à envoyer dans la bdd
 */
class RegistrationHandler
{
    private $userRepository;
    private $userPictureRepository;
    private $userPasswordEncoder;
    private $rankRepository;
    private $tokenGenerator;

    /**
     * @param UserRepository $userRepository
     * @param UserPictureRepository $userPictureRepository
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param RankRepository $rankRepository
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function __construct(
        UserRepository $userRepository,
        UserPictureRepository $userPictureRepository,
        UserPasswordEncoderInterface $userPasswordEncoder,
        RankRepository $rankRepository,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        $this->userRepository = $userRepository;
        $this->userPictureRepository = $userPictureRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->rankRepository = $rankRepository;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param FormInterface $form
     * @param User $user
     * @return bool
     */
    public function registerHandle(
        FormInterface $form,
        User $user
    ): bool
    {
        $findRank = $this->rankRepository->findOneByRolename('Utilisateur');

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordEncoder->encodePassword($user, $user->getPassword());
            $request = Request::createFromGlobals();
            $token = $this->tokenGenerator->generateToken();

            $user->setPassword($password);
            $user->getPicture()->setUser($user);
            $user->setRank($findRank);
            $user->setIp($request->getClientIp());
            $user->setConfirmationAccountToken($token);

            $this->userRepository->save($user);

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function confirmationAccount(
        User $user
    ): bool
    {
        $user->setConfirmationAccountToken(null);
        $user->setConfirmationAccount(true);
        $user->setConfirmationAccountAt(new \DateTimeImmutable());

        $this->userRepository->update($user);

        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function deleteAccount(
        User $user
    ): bool
    {
        $this->userPictureRepository->removeIfInvalidAccount($user->getPicture());
        $this->userRepository->removeIfInvalidAccount($user);

        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function replyToken(
        User $user
    ): bool
    {
        $token = $this->tokenGenerator->generateToken();

        $user->setConfirmationAccountToken($token);

        $this->userRepository->update($user);

        return true;
    }
}
