<?php

namespace App\Controller\Admin\User;

use App\ControllerHandler\Admin\User\UserHandler;
use App\Entity\User\User;
use App\Form\Admin\User\UserEditType;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 *
 * @Route("/admin/user", name="admin_user_")
 */
class UserController extends AbstractController
{
    private UserHandler $userHandler;

    public function __construct(
        UserHandler $userHandler
    )
    {
        $this->userHandler = $userHandler;
    }

    /**
     * @param UserRepository $userRepository
     * @return Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        UserRepository $userRepository
    ): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     *
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        User $user
    ): Response
    {
        $form = $this->createForm(UserEditType::class, $user)->handleRequest($request);

        if ($this->userHandler->editUserHandle($form, $user)) {
            $this->addFlash('success', "La modification de l'utilisateur à été effectuée.");

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     *
     * @Route("/{id}/delete", name="delete", methods={"POST"})
     */
    public function delete(
        Request $request,
        User $user
    ): Response
    {
        if ($this->isCsrfTokenValid('remove'.$user->getId(), $request->request->get('_token'))) {
            $this->userHandler->removeUserHandle($user);

            $this->addFlash('success', "L'utilisateur à bien été supprimé.");
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     *
     * @Route("/{id}/undelete", name="undelete", methods={"POST"})
     */
    public function undelete(
        Request $request,
        User $user
    ): Response
    {
        if ($this->isCsrfTokenValid('unremove'.$user->getId(), $request->request->get('_token'))) {
            $this->userHandler->unremoveUserHandle($user);

            $this->addFlash('success', "L'utilisateur à bien été réhabilité.");
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     *
     * @Route("/{id}/deleteDefinitely", name="delete_definitely", methods={"POST"})
     */
    public function removeUserDefinitely(
        Request $request,
        User $user
    ): Response
    {
        if ($this->isCsrfTokenValid('removeDefinitely'.$user->getId(), $request->request->get('_token'))) {
            $this->userHandler->removeUserDefinitely($user);

            $this->addFlash('success', "L'utilisateur à bien été supprimé définitivement.");
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @param User $user
     * @return Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        User $user
    ): Response
    {
       return $this->render('admin/user/show.html.twig', [
           'user' => $user
       ]) ;
    }
}
