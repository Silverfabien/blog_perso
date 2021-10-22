<?php

namespace App\Controller\User;

use App\ControllerHandler\UserHandler;
use App\Form\User\ResetPasswordType;
use App\Form\User\UserEditType;
use App\Repository\Article\CommentRepository;
use App\Repository\Article\LikeRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
    private Request $request;
    private UserHandler $userHandler;

    public function __construct(
        Request $request,
        UserHandler $userHandler
    )
    {
        $this->request = $request;
        $this->userHandler = $userHandler;
    }

    /**
     * @param CommentRepository $commentRepository
     * @param LikeRepository $likeRepository
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/", name="account", methods={"GET", "POST"})
     */
    public function account(
        CommentRepository $commentRepository,
        LikeRepository $likeRepository
    ): Response
    {
        /* @var $user User */
        $user = $this->getUser();

        // TODO Amélioration avec les EventListener
        // Édition des informations
        $userForm = $this->createForm(UserEditType::class, $user)->handleRequest($this->request);

        if ($this->userHandler->editUserHandle($userForm, $user)) {
            $this->addFlash(
                'success',
                "La modification de vos données a bien été effectué."
            );

            return $this->redirectToRoute('user_account');
        }

        // TODO Amélioration avec les EventListener
        // Édition du mot de passe
        $passwordForm = $this->createForm(ResetPasswordType::class, $user)->handleRequest($this->request);

        if ($this->userHandler->editPasswordHandle($passwordForm, $user)) {
            $this->addFlash(
                'success',
                "La modification de votre mot de passe a bien été effectué."
            );

            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/account.html.twig', [
            'passwordForm' => $passwordForm->createView(),
            'userForm' => $userForm->createView(),
            'nbComment' => count($commentRepository->findBy(['user' => $user])),
            'nbLike' => count($likeRepository->findBy(['user' => $user]))
        ]);
    }

    /**
     * @param User $user
     * @param UserHandler $userHandler
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
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

            $this->container->get('security.token_storage')->setToken();

            $this->addFlash(
                'success',
                "La suppression de votre compte a bien été effectué."
            );
        }

       return $this->redirectToRoute('default');
    }
}
