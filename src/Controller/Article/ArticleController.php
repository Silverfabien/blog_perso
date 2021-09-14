<?php

namespace App\Controller\Article;

use App\Entity\Article\Article;
use App\Repository\Article\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article", name="article_")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(
        ArticleRepository $articleRepository
    ): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll()
        ]);
    }

    /**
     * @Route("/{slug}", name="show")
     */
    public function show(
        Article $article
    )
    {
        if (!$article->getPublish() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article
        ]);
    }
}
