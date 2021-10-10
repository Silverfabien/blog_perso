<?php

namespace App\ControllerHandler\Article;

use App\Entity\Article\Article;
use App\Entity\Article\Like;
use App\Repository\Article\ArticleRepository;
use App\Repository\Article\LikeRepository;
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

    public function seeArticleHandle(
        Article $article
    )
    {
        $article->setSee($article->getSee()+1);

        $this->articleRepository->see($article);

        return true;
    }
}
