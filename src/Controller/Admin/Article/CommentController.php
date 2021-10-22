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
    private Request $request;
    private Comment $comment;
    private CommentHandler $commentHandler;

    public function __construct(
        Request $request,
        Comment $comment,
        CommentHandler $commentHandler
    )
    {
        $this->request = $request;
        $this->comment = $comment;
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
     * @return Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('admin/article/comment/show.html.twig', [
            'comment' => $this->comment
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(): Response
    {
        $form = $this->createForm(ArticleCommentType::class, $this->comment)->handleRequest($this->request);

        if ($this->commentHandler->editCommentHandle($form, $this->comment)) {
            $this->addFlash("success", "Le commentaire à bien été modifié.");

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('admin/article/comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $this->comment
        ]);
    }

    /**
     * @param UserInterface $user
     * @return Response
     *
     * @Route("/{id}/delete", name="delete", methods={"GET", "POST"})
     */
    public function delete(
        UserInterface $user
    ): Response
    {
        if ($this->isCsrfTokenValid('remove'.$this->comment->getId(), $this->request->request->get('_token'))) {
            $this->commentHandler->deleteCommentHandle($this->comment, $user);

            $this->addFlash('success', "Le message à bien été supprimé.");
        }

        return $this->redirectToRoute('admin_comment_index');
    }

    /**
     * @return Response
     *
     * @Route("/{id}/undelete", name="undelete", methods={"GET", "POST"})
     */
    public function undelete(): Response
    {
        if ($this->isCsrfTokenValid('unremove'.$this->comment->getId(), $this->request->request->get('_token'))) {
            $this->commentHandler->undeleteCommentHandle($this->comment);

            $this->addFlash('success', "Le message à bien été réhabilité.");
        }

        return $this->redirectToRoute('admin_comment_index');
    }
}
