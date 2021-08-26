<?php

namespace App\Controller\User;

use App\ControllerHandler\RegistrationHandler;
use App\Entity\User\User;
use App\Form\User\RegistrationType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
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
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @param Request $request
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @param RegistrationHandler $registrationHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response|null
     *
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthenticator $loginFormAuthenticator,
        RegistrationHandler $registrationHandler,
        \Swift_Mailer $mailer
    )
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('default');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user)->handleRequest($request);

        if ($registrationHandler->registerHandle($form, $user)) {
            // Génération du mail
            $confirmUrl = $this->generateUrl('confirmation_account', ['token' => $user->getConfirmationAccountToken()], UrlGeneratorInterface::ABSOLUTE_URL);
            $deleteUrl = $this->generateUrl('delete_account', ['token' => $user->getConfirmationAccountToken()], UrlGeneratorInterface::ABSOLUTE_URL);

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
     * @Route("/confirmation_account/{token}", name="confirmation_account")
     */
    public function confirmationAccount()
    {

    }

    /**
     * @Route("/delete_account/{token}", name="delete_account")
     */
    public function deleteAccountIfInvalid()
    {

    }
}
