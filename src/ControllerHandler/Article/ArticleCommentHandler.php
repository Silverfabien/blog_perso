<?php

namespace App\ControllerHandler\Article;

use App\Entity\Article\Article;
use App\Entity\Article\Comment;
use App\Repository\Article\CommentRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleCommentHandler
{
    private $commentRepository;

    public function __construct(
        CommentRepository $commentRepository
    )
    {
        $this->commentRepository = $commentRepository;
    }

    public function createCommentHandle(
        FormInterface $form,
        Comment $comment,
        Article $article,
        UserInterface $user
    )
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setUser($user);

            $this->commentRepository->save($comment);

            return true;
        }

        return false;
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

    public function removeCommentHandle(
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

    public function unRemoveCommentHandle(
        Comment $comment
    )
    {
        $comment->setDeleted(false);
        $comment->setUndeletedAt(new \DateTimeImmutable());

        $this->commentRepository->update($comment);

        return true;
    }

    public function removeDefinitely(
        Comment $comment
    )
    {
        $this->commentRepository->remove($comment);

        return true;
    }
}
