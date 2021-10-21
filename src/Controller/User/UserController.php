<?php

namespace App\Controller\User;

use App\ControllerHandler\UserHandler;
use App\Form\User\ResetPasswordType;
use App\Form\User\UserEditType;
use App\Repository\Article\CommentRepository;
use App\Repository\Article\LikeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User\User;

/**
 * Class UserController
 *
 * @Route("/account", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserHandler $userHandler
     * @return Response
     *
     * @Route("/", name="account", methods={"GET", "POST"})
     */
    public function account(
        Request $request,
        UserHandler $userHandler,
        CommentRepository $commentRepository,
        LikeRepository $likeRepository
    ): Response
    {
        /* @var $user User */
        $user = $this->getUser();

        // TODO Amélioration avec les EventListener
        // Édition des informations
        $userForm = $this->createForm(UserEditType::class, $user)->handleRequest($request);

        if ($userHandler->editUserHandle($userForm, $user)) {
            $this->addFlash(
                'success',
                "La modification de vos données a bien été effectué."
            );

            return $this->redirectToRoute('user_account');
        }

        // TODO Amélioration avec les EventListener
        // Édition du mot de passe
        $passwordForm = $this->createForm(ResetPasswordType::class, $user)->handleRequest($request);

        if ($userHandler->editPasswordHandle($passwordForm, $user)) {
            $this->addFlash(
                'success',
                "La modification de votre mot de passe a bien été effectué."
            );

            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/account.html.twig', [
            'passwordForm' => $passwordForm->createView(),
            'userForm' => $userForm->createView(),
            'nbComment' => count($commentRepository->findByUser($user)),
            'nbLike' => count($likeRepository->findByUser($user))
        ]);
    }

    /**
     * @param User $user
     * @param UserHandler $userHandler
     * @return Response
     *
     * @Route("/delete/{id}", name="account_remove", methods={"POST"})
     */
    public function deletedAccount(
        User $user,
        UserHandler $userHandler,
        Request $request
    ): Response
    {
        if ($this->isCsrfTokenValid('remove'.$user->getId(), $request->request->get('_token'))) {
            $userHandler->deletedHandle($user);

            $this->container->get('security.token_storage')->setToken(null);

            $this->addFlash(
                'success',
                "La suppression de votre compte a bien été effectué."
            );
        }

       return $this->redirectToRoute('default');
    }
}
