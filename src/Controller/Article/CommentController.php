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
    private Request $request;
    private Comment $comment;
    private ArticleCommentHandler $articleCommentHandler;

    public function __construct(
        Request $request,
        Comment $comment,
        ArticleCommentHandler $articleCommentHandler
    )
    {
        $this->request = $request;
        $this->comment = $comment;
        $this->articleCommentHandler = $articleCommentHandler;
    }

    /**
     * @param UserInterface $user
     * @return Response
     *
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(
        UserInterface $user
    ): Response
    {
        if ($this->getUser() !== $this->comment->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        if ($this->isCsrfTokenValid('delete'.$this->comment->getId(), $this->request->request->get('_token'))) {
            $this->articleCommentHandler->removeCommentHandle($this->comment, $user);
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * @return Response
     *
     * @Route("/{id}/undelete", name="undelete")
     */
    public function unDelete(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('unDelete'.$this->comment->getId(), $this->request->request->get('_token'))) {
            $this->articleCommentHandler->unRemoveCommentHandle($this->comment);
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * @return Response
     *
     * @Route("/{id}/delete_definitely", name="delete_definitely")
     */
    public function deleteDefinitely(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('deleteDefinitely'.$this->comment->getId(), $this->request->request->get('_token'))) {
            $this->articleCommentHandler->removeDefinitely($this->comment);
        }

        return $this->redirectToRoute('article_index');
    }
}
