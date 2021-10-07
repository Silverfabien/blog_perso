<?php

namespace App\ControllerHandler\Admin\Article;

use App\Entity\Article\Comment;
use App\Repository\Article\CommentRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentHandler
{
    private $commentRepository;

    public function __construct(
        CommentRepository $commentRepository
    )
    {
        $this->commentRepository = $commentRepository;
    }

    public function editCommentHandle(
        FormInterface $form,
        Comment $comment
    )
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt(new \DateTimeImmutable());

            $this->commentRepository->update($comment);

            return true;
        }

        return false;
    }

    public function deleteCommentHandle(
        Comment $comment,
        UserInterface $user
    )
    {
        $comment->setDeleted(true);
        $comment->setDeletedAt(new \DateTimeImmutable());
        $comment->setDeletedByUser($user);

        $this->commentRepository->update($comment);

        return true;
    }

    public function undeleteCommentHandle(
        Comment $comment
    )
    {
        $comment->setDeleted(false);
        $comment->setUndeletedAt(new \DateTimeImmutable());

        $this->commentRepository->update($comment);

        return true;
    }
}
