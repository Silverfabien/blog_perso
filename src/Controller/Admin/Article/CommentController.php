<?php

namespace App\Controller\Admin\Article;

use App\ControllerHandler\Admin\Article\CommentHandler;
use App\Entity\Article\Comment;
use App\Form\Article\ArticleCommentType;
use App\Repository\Article\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/admin/comment", name="admin_comment_")
 */
class CommentController extends AbstractController
{
    private CommentHandler $commentHandler;

    public function __construct(
        CommentHandler $commentHandler
    )
    {
        $this->commentHandler = $commentHandler;
    }

    /**
     * @param CommentRepository $commentRepository
     * @return Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        CommentRepository $commentRepository
    ): Response
    {
        return $this->render('admin/article/comment/index.html.twig', [
            'comments' => $commentRepository->findAll()
        ]);
    }

    /**
     * @param Comment $comment
     * @return Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        Comment $comment
    ): Response
    {
        return $this->render('admin/article/comment/show.html.twig', [
            'comment' => $comment
        ]);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return Response
     *
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(
        Request $request,
        Comment $comment
    ): Response
    {
        $form = $this->createForm(ArticleCommentType::class, $comment)->handleRequest($request);

        if ($this->commentHandler->editCommentHandle($form, $comment)) {
            $this->addFlash("success", "Le commentaire à bien été modifié.");

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('admin/article/comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * @param UserInterface $user
     * @param Request $request
     * @param Comment $comment
     * @return Response
     *
     * @Route("/{id}/delete", name="delete", methods={"GET", "POST"})
     */
    public function delete(
        UserInterface $user,
        Request $request,
        Comment $comment
    ): Response
    {
        if ($this->isCsrfTokenValid('remove'.$comment->getId(), $request->request->get('_token'))) {
            $this->commentHandler->deleteCommentHandle($comment, $user);

            $this->addFlash('success', "Le message à bien été supprimé.");
        }

        return $this->redirectToRoute('admin_comment_index');
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return Response
     *
     * @Route("/{id}/undelete", name="undelete", methods={"GET", "POST"})
     */
    public function undelete(
        Request $request,
        Comment $comment
    ): Response
    {
        if ($this->isCsrfTokenValid('unremove'.$comment->getId(), $request->request->get('_token'))) {
            $this->commentHandler->undeleteCommentHandle($comment);

            $this->addFlash('success', "Le message à bien été réhabilité.");
        }

        return $this->redirectToRoute('admin_comment_index');
    }
}
