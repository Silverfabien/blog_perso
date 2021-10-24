<?php

namespace App\ControllerHandler\Admin\Article;

use App\Entity\Article\Comment;
use App\Repository\Article\CommentRepository;
use DateTimeImmutable;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentHandler
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
    public function deleteCommentHandle(
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
    public function undeleteCommentHandle(
        Comment $comment
    ): bool
    {
        $comment->setDeleted(false);
        $comment->setUndeletedAt(new DateTimeImmutable());

        $this->commentRepository->update($comment);

        return true;
    }
}
