<?php

namespace App\ControllerHandler\Article;

use App\Entity\Article\Article;
use App\Entity\Article\Like;
use App\Repository\Article\ArticleRepository;
use App\Repository\Article\LikeRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleHandler
{
    private ArticleRepository $articleRepository;
    private LikeRepository $likeRepository;

    public function __construct(
        ArticleRepository $articleRepository,
        LikeRepository $likeRepository
    )
    {
        $this->articleRepository = $articleRepository;
        $this->likeRepository = $likeRepository;
    }

    /**
     * @param Like $like
     * @param Article $article
     * @param UserInterface $user
     * @return bool
     */
    public function likeHandle(
        Like $like,
        Article $article,
        UserInterface $user
    ): bool
    {
        $like->setArticle($article);
        $like->setUser($user);

        $this->likeRepository->like($like);

        return true;
    }

    /**
     * @param Article $article
     * @return bool
     */
    public function seeArticleHandle(
        Article $article
    ): bool
    {
        $article->setSee($article->getSee()+1);

        $this->articleRepository->see($article);

        return true;
    }
}
