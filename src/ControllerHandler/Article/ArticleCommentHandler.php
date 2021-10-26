<?php

namespace App\ControllerHandler\Article;

use App\Entity\Article\Article;
use App\Entity\Article\Comment;
use App\Repository\Article\CommentRepository;
use DateTimeImmutable;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleCommentHandler
{
    private CommentRepository $commentRepository;

    public function __construct(
        CommentRepository $commentRepository
    )
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * @param FormInterface $form
     * @param Comment $comment
     * @param Article $article
     * @param UserInterface $user
     * @return bool
     */
    public function createCommentHandle(
        FormInterface $form,
        Comment $comment,
        Article $article,
        UserInterface $user
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setUser($user);

            $this->commentRepository->save($comment);

            return true;
        }

        return false;
    }

    /**
     * @param FormInterface $form
     * @param Comment $comment
     * @return bool
     */
    public function editCommentHandle(
        FormInterface $form,
        Comment $comment
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt(new DateTimeImmutable());

            $this->commentRepository->update($comment);

            return true;
        }

        return false;
    }

    /**
     * @param Comment $comment
     * @param UserInterface $user
     * @return bool
     */
    public function removeCommentHandle(
        Comment $comment,
        UserInterface $user
    ): bool
    {
        $comment->setDeleted(true);
        $comment->setDeletedAt(new DateTimeImmutable());
        $comment->setDeletedByUser($user);

        $this->commentRepository->update($comment);

        return true;
    }

    /**
     * @param Comment $comment
     * @return bool
     */
    public function unRemoveCommentHandle(
        Comment $comment
    ): bool
    {
        $comment->setDeleted(false);
        $comment->setUndeletedAt(new DateTimeImmutable());

        $this->commentRepository->update($comment);

        return true;
    }

    /**
     * @param Comment $comment
     * @return bool
     */
    public function removeDefinitely(
        Comment $comment
    ): bool
    {
        $this->commentRepository->remove($comment);

        return true;
    }
}
