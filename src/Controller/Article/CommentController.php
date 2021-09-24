<?php

namespace App\Controller\Article;

use App\ControllerHandler\Article\ArticleCommentHandler;
use App\Entity\Article\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/comment", name="article_comment_")
 */
class CommentController extends AbstractController
{
    /**
     * @param Request $request
     * @param Comment $comment
     * @param ArticleCommentHandler $commentHandler
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(
        Request $request,
        Comment $comment,
        ArticleCommentHandler $commentHandler,
        UserInterface $user
    )
    {
        if ($this->getUser() !== $comment->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentHandler->removeCommentHandle($comment, $user);
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param ArticleCommentHandler $commentHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/{id}/undelete", name="undelete")
     */
    public function unDelete(
        Request $request,
        Comment $comment,
        ArticleCommentHandler $commentHandler
    )
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('unDelete'.$comment->getId(), $request->request->get('_token'))) {
            $commentHandler->unRemoveCommentHandle($comment);
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param ArticleCommentHandler $commentHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/{id}/delete_definitely", name="delete_definitely")
     */
    public function deleteDefinitely(
        Request $request,
        Comment $comment,
        ArticleCommentHandler $commentHandler
    )
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('deleteDefinitely'.$comment->getId(), $request->request->get('_token'))) {
            $commentHandler->removeDefinitely($comment);
        }

        return $this->redirectToRoute('article_index');
    }
}
