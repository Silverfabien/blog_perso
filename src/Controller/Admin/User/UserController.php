<?php

namespace App\Controller\Admin\User;

use App\ControllerHandler\Admin\User\UserHandler;
use App\Entity\User\User;
use App\Form\Admin\User\UserEditType;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user", name="admin_user_")
 */
class UserController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        UserRepository $userRepository
    )
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        User $user,
        UserHandler $userHandler
    )
    {
        $form = $this->createForm(UserEditType::class, $user)->handleRequest($request);

        if ($userHandler->editUserHandle($form, $user)) {
            $this->addFlash('success', "La modification de l'utilisateur à été effectuée.");

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"})
     */
    public function delete(
        Request $request,
        User $user,
        UserHandler $userHandler
    )
    {
        if ($this->isCsrfTokenValid('remove'.$user->getId(), $request->request->get('_token'))) {
            $userHandler->removeUserHandle($user);

            $this->addFlash('success', "L'utilisateur à bien été supprimé.");
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @Route("/{id}/undelete", name="undelete", methods={"POST"})
     */
    public function undelete(
        Request $request,
        User $user,
        UserHandler $userHandler
    )
    {
        if ($this->isCsrfTokenValid('unremove'.$user->getId(), $request->request->get('_token'))) {
            $userHandler->unremoveUserHandle($user);

            $this->addFlash('success', "L'utilisateur à bien été réabilité.");
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @Route("/{id}/deleteDefinitely", name="delete_definitely", methods={"POST"})
     */
    public function removeUserDefinitely(
        Request $request,
        User $user,
        UserHandler $userHandler
    )
    {
        if ($this->isCsrfTokenValid('removeDefinitely'.$user->getId(), $request->request->get('_token'))) {
            $userHandler->removeUserDefinitely($user);

            $this->addFlash('success', "L'utilisateur à bien été supprimé définitivement.");
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @param User $user
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        User $user
    )
    {
       return $this->render('admin/user/show.html.twig', [
           'user' => $user
       ]) ;
    }
}
