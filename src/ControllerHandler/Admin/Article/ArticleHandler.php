<?php

namespace App\ControllerHandler\Admin\Article;

use App\Entity\Article\Article;
use App\Entity\Article\Like;
use App\Repository\Article\ArticleRepository;
use App\Repository\Article\LikeRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleHandler
{
    private $articleRepository;
    private $likeRepository;

    public function __construct(
        ArticleRepository $articleRepository,
        LikeRepository $likeRepository
    )
    {
        $this->articleRepository = $articleRepository;
        $this->likeRepository = $likeRepository;
    }

    public function createArticleHandle(
        FormInterface $form,
        Article $article,
        UserInterface $user
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($user);

            $this->articleRepository->save($article);

            return true;
        }

        return false;
    }

    public function editArticleHandle(
        FormInterface $form,
        Article $article,
        UserInterface $user
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new \DateTimeImmutable());
            $article->setAuthorEdit($user);

            $this->articleRepository->update($article);

            return true;
        }

        return false;
    }

    public function deleteArticleHandle(
        Article $article
    ): bool
    {
        $this->articleRepository->remove($article);

        return true;
    }

    public function likeHandle(
        Like $like,
        Article $article,
        UserInterface $user
    )
    {
        $like->setArticle($article);
        $like->setUser($user);

        $this->likeRepository->like($like);

        return true;
    }
}
