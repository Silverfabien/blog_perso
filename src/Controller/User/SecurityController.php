<?php

namespace App\Controller\User;

use App\ControllerHandler\RegistrationHandler;
use App\ControllerHandler\UserHandler;
use App\Entity\User\User;
use App\Form\User\ForgotPasswordType;
use App\Form\User\RegistrationType;
use App\Form\User\ResetForgotPasswordType;
use App\Repository\User\UserRepository;
use App\Security\LoginFormAuthenticator;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use LogicException;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 */
class SecurityController extends AbstractController
{
    private RegistrationHandler $registrationHandler;
    private Swift_Mailer $mailer;
    private Request $request;
    private UserHandler $userHandler;
    private UserRepository $userRepository;

    public function __construct(
        RegistrationHandler $registrationHandler,
        Swift_Mailer $mailer,
        Request $request,
        UserHandler $userHandler,
        UserRepository $userRepository
    )
    {
        $this->registrationHandler = $registrationHandler;
        $this->mailer = $mailer;
        $this->request = $request;
        $this->userHandler = $userHandler;
        $this->userRepository = $userRepository;
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     *
     * @Route("/login", name="app_login")
     */
    public function login(
        AuthenticationUtils $authenticationUtils
    ): Response
    {
         if ($this->getUser()) {
             $this->addFlash(
                 'warning',
                 "Vous êtes déjà connecté."
             );

             return $this->redirectToRoute('default');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @return Response
     *
     * @Route("/register", name="app_register", methods={"GET", "POST"})
     */
    public function register(
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthenticator $loginFormAuthenticator
    ): Response
    {
        if ($this->getUser()) {
            $this->addFlash(
                'warning',
                "Vous êtes déjà connecté."
            );

            return $this->redirectToRoute('default');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user)->handleRequest($this->request);

        if ($this->registrationHandler->registerHandle($form, $user)) {
            // Génération du mail via une protected function
            $mail = $this->mailConfirmAccount($user);

            $this->mailer->send($mail);

            $this->addFlash(
                'success',
                sprintf(
                    "Un email de confirmation à l'adresse mail \"%s\" vous a été envoyé.",
                    $user->getEmail()
                )
            );

            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $user,
                $this->request,
                $loginFormAuthenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param String $token
     * @return Response
     *
     * @Route("/confirmation_account/{token}", name="confirmation_account",methods={"POST"})
     */
    public function confirmationAccount(
        String $token
    ): Response
    {
        /* @var $user User */
        $user = $this->getUser();

        if ($user === null) {
            $this->addFlash(
                'danger',
                "Une erreur est survenue."
            );

            return $this->redirectToRoute('default');
        }

        if ($token === $user->getConfirmationAccountToken()) {
            $this->registrationHandler->confirmationAccount($user);

            $this->addFlash(
                'success',
                "Votre compte à bien été confirmé."
            );

            return $this->redirectToRoute('default');
        }

        $this->addFlash(
            'warning',
            "Votre devez être connecté à votre compte pour le validé."
        );

        return $this->redirectToRoute('default');
    }

    /**
     * @return Response
     *
     * @Route("/delete_account/{token}", name="delete_account", methods={"POST"})
     */
    public function deleteAccountIfInvalid(): Response
    {
        /* @var $user User */
        $user = $this->getUser();

        if ($user === null) {
            $this->addFlash(
                'danger',
                "Une erreur est survenue."
            );

            return $this->redirectToRoute('default');
        }

        if ($user->getConfirmationAccount() === false) {
            // Déconnexion forcer de l'utilisateur avant la suppression
            $this->container->get('security.token_storage')->setToken();

            $this->registrationHandler->deleteAccount($user);

            $this->addFlash(
                'success',
                "le compte a bien été supprimé."
            );

            return $this->redirectToRoute('default');
        }

        $this->addFlash(
            'warning',
            "Vous êtes connecté, la suppression ne sera pas effectué."
        );
        return $this->redirectToRoute('default');
    }

    /**
     * @return Response
     *
     * @Route("/reply_confirmation_account", name="reply_confirmation_account", methods={"POST"})
     */
    public function replyEmailConfirmationAccount(): Response
    {
        /* @var $user User */
        $user = $this->getUser();

        if ($user === null) {
            $this->addFlash(
                'danger',
                "Une erreur est survenue."
            );

            return $this->redirectToRoute('default');
        }

        if ($user->getConfirmationAccount() === false && $this->registrationHandler->replyToken($user)) {
            // Génération du mail via une protected function
            $mail = $this->mailConfirmAccount($user);

            $this->mailer->send($mail);

            $this->addFlash(
                'success',
                sprintf(
                    "Un email de confirmation à l'adresse mail \"%s\" vous a été envoyé.",
                    $user->getEmail()
                )
            );

            return $this->redirectToRoute('default');
        }

        $this->addFlash(
            'danger',
            "Votre compte est déjà confirmé."
        );

        return $this->redirectToRoute('default');
    }

    /**
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/forgot_password", name="forgot_password", methods={"GET", "POST"})
     */
    public function forgotPassword(): Response
    {
        if ($this->getUser()) {
            $this->addFlash(
                'warning',
                "Vous êtes déjà connecté."
            );

            return $this->redirectToRoute('default');
        }

        $form = $this->createForm(ForgotPasswordType::class)->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()->getEmail();
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if ($user === null) {
                $this->addFlash(
                    'success',
                    "Un email de confirmation vous a été envoyé."
                );

                return $this->redirectToRoute('forgot_password');
            }

            if ($this->userHandler->generateResetTokenHandle($user)) {
                $url = $this->generateUrl(
                    'reset_forgot_password',
                    ['token' => $user->getResetToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $mail = (new Swift_Message('Reset du mot de passe'))
                    ->setFrom('hollebeque.fabien@silversat.ovh')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'security/_resetPasswordMail.html.twig',
                            compact('url', 'user')
                        ), 'text/html'
                    )
                ;

                $this->mailer->send($mail);

                $this->addFlash(
                    'success',
                    "Un email de confirmation vous a été envoyé."
                );

                return $this->redirectToRoute('forgot_password');
            }
        }

        return $this->render('security/forgotPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param $token
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/reset_forgot_password/{token}", name="reset_forgot_password", methods={"GET", "POST"})
     */
    public function resetForgotPassword(
        $token
    ): Response
    {
        if ($this->getUser()) {
            $this->addFlash(
                'warning',
                "Vous êtes déjà connecté."
            );

            return $this->redirectToRoute('default');
        }

        $form = $this->createForm(ResetForgotPasswordType::class)->handleRequest($this->request);
        $user = $this->userRepository->findOneBy(['resetToken' => $token]);

        // Si le token n'existe pas
        if ($user === null) {
            $this->addFlash(
                'warning',
                "Le lien que vous avez demandé n'existe pas ou a été expiré."
            );

            return $this->redirectToRoute('default');
        }

        // Si le token est expiré
        if (date_timestamp_get($user->getResetTokenExpiredAt()) <= date_timestamp_get(new DateTimeImmutable())) {
            $this->userHandler->expiredResetTokenHandle($user);

            $this->addFlash(
                'warning',
                "Le lien que vous avez demandé n'existe pas ou a été expiré."
            );

            return $this->redirectToRoute('default');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()->getEmail();

            if ($email === $user->getEmail()) {
                $this->userHandler->resetPasswordHandle($form, $user);


                $this->addFlash(
                    'success',
                    "La réinitialisation de votre mot de passe a bien été effectué."
                );
                return $this->redirectToRoute('default');
            }

            $this->addFlash(
                'warning',
                "L'email que vous avez indiqué ne correspond pas à votre compte."
            );
            return $this->redirectToRoute('reset_forgot_password', ['token' => $token]);
        }

        return $this->render('security/resetForgotPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param User $user
     * @return Swift_Message
     */
    protected function mailConfirmAccount(
        User $user
    ): Swift_Message
    {
        $confirmUrl = $this->generateUrl(
            'confirmation_account',
            ['token' => $user->getConfirmationAccountToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $deleteUrl = $this->generateUrl(
            'delete_account',
            ['token' => $user->getConfirmationAccountToken()],
            UrlGeneratorInterface::ABSOLUTE_URL)
        ;

        return (new Swift_Message('Validation de votre compte'))
            ->setFrom('hollebeque.fabien@silversat.ovh')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'security/_confirmationMail.html.twig',
                    compact('confirmUrl', 'deleteUrl', 'user')
                ), 'text/html'
            );
    }
}
