<?php

namespace App\ControllerHandler\Admin\Article;

use App\Entity\Article\Article;
use App\Repository\Article\ArticleRepository;
use DateTimeImmutable;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleHandler
{
    private ArticleRepository $articleRepository;

    public function __construct(
        ArticleRepository $articleRepository
    )
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param FormInterface $form
     * @param Article $article
     * @param UserInterface $user
     * @return bool
     */
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

    /**
     * @param FormInterface $form
     * @param Article $article
     * @param UserInterface $user
     * @return bool
     */
    public function editArticleHandle(
        FormInterface $form,
        Article $article,
        UserInterface $user
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new DateTimeImmutable());
            $article->setAuthorEdit($user);

            $this->articleRepository->update($article);

            return true;
        }

        return false;
    }

    /**
     * @param Article $article
     * @return bool
     */
    public function deleteArticleHandle(
        Article $article
    ): bool
    {
        $this->articleRepository->remove($article);

        return true;
    }

    /**
     * @param Article $article
     * @return bool
     */
    public function publishHandle(
        Article $article
    ): bool
    {
        $article->setPublish(true);
        $article->setPublishedAt(new DateTimeImmutable());

        $this->articleRepository->update($article);

        return true;
    }

    /**
     * @param Article $article
     * @return bool
     */
    public function unpublishHandle(
        Article $article
    ): bool
    {
        $article->setPublish(false);

        $this->articleRepository->update($article);

        return true;
    }
}
