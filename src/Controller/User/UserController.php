<?php

namespace App\Controller\User;

use App\ControllerHandler\UserHandler;
use App\Form\User\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User\User;

class UserController extends AbstractController
{
    /**
     * @Route("/account", name="user_account")
     */
    public function account(
        Request $request,
        UserHandler $userHandler
    ): Response
    {
        /* @var $user User */
        $user = $this->getUser();

        // TODO Amélioration vers les EventListener
        // Édition du mot de passe
        $passwordForm = $this->createForm(ResetPasswordType::class, $user)->handleRequest($request);

        if ($userHandler->editPasswordHandle($passwordForm, $user)) {
            // TODO addFlash success

            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/account.html.twig', [
            'passwordForm' => $passwordForm->createView()
        ]);
    }
}
