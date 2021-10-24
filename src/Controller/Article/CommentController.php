<?php

namespace App\Controller\Article;

use App\ControllerHandler\Article\ArticleCommentHandler;
use App\Entity\Article\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class CommentController
 *
 * @Route("/comment", name="article_comment_")
 */
class CommentController extends AbstractController
{
    private ArticleCommentHandler $articleCommentHandler;

    public function __construct(
        ArticleCommentHandler $articleCommentHandler
    )
    {
        $this->articleCommentHandler = $articleCommentHandler;
    }

    /**
     * @param UserInterface $user
     * @param Request $request
     * @param Comment $comment
     * @return Response
     *
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(
        UserInterface $user,
        Request $request,
        Comment $comment
    ): Response
    {
        if ($this->getUser() !== $comment->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $this->articleCommentHandler->removeCommentHandle($comment, $user);
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return Response
     *
     * @Route("/{id}/undelete", name="undelete")
     */
    public function unDelete(
        Request $request,
        Comment $comment
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('unDelete'.$comment->getId(), $request->request->get('_token'))) {
            $this->articleCommentHandler->unRemoveCommentHandle($comment);
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return Response
     *
     * @Route("/{id}/delete_definitely", name="delete_definitely")
     */
    public function deleteDefinitely(
        Request $request,
        Comment $comment
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('deleteDefinitely'.$comment->getId(), $request->request->get('_token'))) {
            $this->articleCommentHandler->removeDefinitely($comment);
        }

        return $this->redirectToRoute('article_index');
    }
}
