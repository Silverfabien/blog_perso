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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * SecurityController
 */
class SecurityController extends AbstractController
{
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
             // TODO addFlash warning
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
        // TODO addFlash warning
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @param Request $request
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @param RegistrationHandler $registrationHandler
     * @param \Swift_Mailer $mailer
     * @return Response
     *
     * @Route("/register", name="app_register", methods={"GET", "POST"})
     */
    public function register(
        Request $request,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthenticator $loginFormAuthenticator,
        RegistrationHandler $registrationHandler,
        \Swift_Mailer $mailer
    ): Response
    {
        if ($this->getUser()) {
            // TODO addFlash warning
            return $this->redirectToRoute('default');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user)->handleRequest($request);

        if ($registrationHandler->registerHandle($form, $user)) {
            // Génération du mail
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

            $mail = (new \Swift_Message('Validation de votre compte'))
                ->setFrom('hollebeque.fabien@silversat.ovh')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'security/_confirmationMail.html.twig',
                        compact('confirmUrl', 'deleteUrl', 'user')
                    ), 'text/html'
                )
            ;

            $mailer->send($mail);

            // TODO addFlash success
            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
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
     * @param RegistrationHandler $registrationHandler
     * @return Response
     *
     * @Route("/confirmation_account/{token}", name="confirmation_account",methods={"POST"})
     */
    public function confirmationAccount(
        String $token,
        RegistrationHandler $registrationHandler
    ): Response
    {
        /* @var $user User */
        $user = $this->getUser();

        if ($user === null) {
            // TODO addFlash danger
            return $this->redirectToRoute('default');
        }

        if ($token === $user->getConfirmationAccountToken()) {
            $registrationHandler->confirmationAccount($user);

            // TODO addFlash success
            return $this->redirectToRoute('default');
        }

        // TODO addFlash warning
        return $this->redirectToRoute('default');
    }

    /**
     * @param RegistrationHandler $registrationHandler
     * @return Response
     *
     * @Route("/delete_account/{token}", name="delete_account", methods={"POST"})
     */
    public function deleteAccountIfInvalid(
        RegistrationHandler $registrationHandler
    ): Response
    {
        /* @var $user User */
        $user = $this->getUser();

        if ($user === null) {
            // TODO addFlash danger
            return $this->redirectToRoute('default');
        }

        if ($user->getConfirmationAccount() === false) {
            // Déconnexion forcer de l'utilisateur avant la suppression
            $this->container->get('security.token_storage')->setToken(null);

            $registrationHandler->deleteAccount($user);

            // TODO addFlash success
            return $this->redirectToRoute('default');
        }

        // TODO addFlash warning
        return $this->redirectToRoute('default');
    }

    /**
     * @param \Swift_Mailer $mailer
     * @param RegistrationHandler $registrationHandler
     * @return Response
     *
     * @Route("/reply_confirmation_account", name="reply_confirmation_account", methods={"POST"})
     */
    public function replyEmailConfirmationAccount(
        \Swift_Mailer $mailer,
        RegistrationHandler $registrationHandler
    ): Response
    {
        /* @var $user User */
        $user = $this->getUser();

        if ($user === null) {
            // TODO addFlash danger
            return $this->redirectToRoute('default');
        }

        if ($user->getConfirmationAccount() === false and $registrationHandler->replyToken($user)) {
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

            $mail = (new \Swift_Message('Validation de votre compte'))
                ->setFrom('hollebeque.fabien@silversat.ovh')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'security/_confirmationMail.html.twig',
                        compact('confirmUrl', 'deleteUrl', 'user')
                    ), 'text/html'
                )
            ;

            $mailer->send($mail);

            // TODO addFlash success
            return $this->redirectToRoute('default');
        }

        // TODO addFlash danger
        return $this->redirectToRoute('default');
    }

    /**
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param UserHandler $userHandler
     * @param UserRepository $userRepository
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/forgot_password", name="forgot_password", methods={"GET", "POST"})
     */
    public function forgotPassword(
        Request $request,
        \Swift_Mailer $mailer,
        UserHandler $userHandler,
        UserRepository $userRepository
    ): Response
    {
        if ($this->getUser()) {
            // TODO addFlash warning
            return $this->redirectToRoute('default');
        }

        $form = $this->createForm(ForgotPasswordType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()->getEmail();
            $user = $userRepository->findOneByEmail($email);

            if ($user === null) {
                // TODO addFlash success
                return $this->redirectToRoute('default');
            }

            if ($userHandler->generateResetTokenHandle($user)) {
                $url = $this->generateUrl(
                    'reset_forgot_password',
                    ['token' => $user->getResetToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $mail = (new \Swift_Message('Reset du mot de passe'))
                    ->setFrom('hollebeque.fabien@silversat.ovh')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'user/_resetPasswordMail.html.twig',
                            compact('url', 'user')
                        ), 'text/html'
                    )
                ;

                $mailer->send($mail);

                // TODO addFlash success
                return $this->redirectToRoute('default');
            }
        }

        return $this->render('user/forgotPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserHandler $userHandler
     * @param $token
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/reset_forgot_password/{token}", name="reset_forgot_password", methods={"GET", "POST"})
     */
    public function resetForgotPassword(
        Request $request,
        UserRepository $userRepository,
        UserHandler $userHandler,
        $token
    ): Response
    {
        if ($this->getUser()) {
            // TODO addFlash warning
            return $this->redirectToRoute('default');
        }

        $form = $this->createForm(ResetForgotPasswordType::class)->handleRequest($request);
        $user = $userRepository->findOneByResetToken($token);

        // Si le token n'existe pas
        if ($user === null) {
            // TODO addFlash danger
            return $this->redirectToRoute('default');
        }

        // Si le token est expiré
        if (date_timestamp_get($user->getResetTokenExpiredAt()) <= date_timestamp_get(new \DateTimeImmutable())) {
            $userHandler->expiredResetTokenHandle($user);

            // TODO addFlash warning
            return $this->redirectToRoute('default');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()->getEmail();

            if ($email === $user->getEmail()) {
                $userHandler->resetPasswordHandle($form, $user);

                // TODO addFlash success
                return $this->redirectToRoute('default');
            }
            // TODO addFlash error
            return $this->redirectToRoute('default');
        }

        return $this->render('user/resetForgotPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
