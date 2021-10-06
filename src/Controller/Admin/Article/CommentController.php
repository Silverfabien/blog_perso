<?php

namespace App\Controller\Admin\Article;

use App\ControllerHandler\Admin\Article\CommentHandler;
use App\Entity\Article\Comment;
use App\Form\Article\ArticleCommentType;
use App\Repository\Article\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/admin/comment", name="admin_comment_")
 */
class CommentController extends AbstractController
{
    /**
     * @param CommentRepository $commentRepository
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        CommentRepository $commentRepository
    )
    {
        return $this->render('admin/article/comment/index.html.twig', [
            'comments' => $commentRepository->findAll()
        ]);
    }

    /**
     * @param Comment $comment
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        Comment $comment
    )
    {
        return $this->render('admin/article/comment/show.html.twig', [
            'comment' => $comment
        ]);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param CommentHandler $commentHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(
        Request $request,
        Comment $comment,
        CommentHandler $commentHandler
    )
    {
        $form = $this->createForm(ArticleCommentType::class, $comment)->handleRequest($request);

        if ($commentHandler->editCommentHandle($form, $comment)) {
            $this->addFlash("success", "Le commentaire à bien été modifié.");

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('admin/article/comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param CommentHandler $commentHandler
     *
     * @Route("/{id}/delete", name="delete", methods={"GET", "POST"})
     */
    public function delete(
        Request $request,
        Comment $comment,
        CommentHandler $commentHandler,
        UserInterface $user
    )
    {
        if ($this->isCsrfTokenValid('remove'.$comment->getId(), $request->request->get('_token'))) {
            $commentHandler->deleteCommentHandle($comment, $user);

            $this->addFlash('success', "Le message à bien été supprimé.");
        }

        return $this->redirectToRoute('admin_comment_index');
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param CommentHandler $commentHandler
     *
     * @Route("/{id}/undelete", name="undelete", methods={"GET", "POST"})
     */
    public function undelete(
        Request $request,
        Comment $comment,
        CommentHandler $commentHandler
    )
    {
        if ($this->isCsrfTokenValid('unremove'.$comment->getId(), $request->request->get('_token'))) {
            $commentHandler->undeleteCommentHandle($comment);

            $this->addFlash('success', "Le message à bien été réabilité.");
        }

        return $this->redirectToRoute('admin_comment_index');
    }
}
