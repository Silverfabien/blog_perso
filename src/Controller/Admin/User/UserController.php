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
    private Request $request;
    private User $user;
    private UserHandler $userHandler;

    public function __construct(
        Request $request,
        User $user,
        UserHandler $userHandler
    )
    {
        $this->request = $request;
        $this->user = $user;
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
     * @return Response
     *
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(): Response
    {
        $form = $this->createForm(UserEditType::class, $this->user)->handleRequest($this->request);

        if ($this->userHandler->editUserHandle($form, $this->user)) {
            $this->addFlash('success', "La modification de l'utilisateur à été effectuée.");

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{id}/delete", name="delete", methods={"POST"})
     */
    public function delete(): Response
    {
        if ($this->isCsrfTokenValid('remove'.$this->user->getId(), $this->request->request->get('_token'))) {
            $this->userHandler->removeUserHandle($this->user);

            $this->addFlash('success', "L'utilisateur à bien été supprimé.");
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @return Response
     *
     * @Route("/{id}/undelete", name="undelete", methods={"POST"})
     */
    public function undelete(): Response
    {
        if ($this->isCsrfTokenValid('unremove'.$this->user->getId(), $this->request->request->get('_token'))) {
            $this->userHandler->unremoveUserHandle($this->user);

            $this->addFlash('success', "L'utilisateur à bien été réhabilité.");
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @return Response
     *
     * @Route("/{id}/deleteDefinitely", name="delete_definitely", methods={"POST"})
     */
    public function removeUserDefinitely(): Response
    {
        if ($this->isCsrfTokenValid('removeDefinitely'.$this->user->getId(), $this->request->request->get('_token'))) {
            $this->userHandler->removeUserDefinitely($this->user);

            $this->addFlash('success', "L'utilisateur à bien été supprimé définitivement.");
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @return Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(): Response
    {
       return $this->render('admin/user/show.html.twig', [
           'user' => $this->user
       ]) ;
    }
}
