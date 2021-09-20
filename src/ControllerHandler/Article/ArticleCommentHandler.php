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

    public function createCommentHandler(
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
}
