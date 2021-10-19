<?php

namespace App\Controller;

use App\Repository\Article\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 *
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * Page index
     *
     * @return Response
     *
     * @Route("/", name="default")
     */
    public function index(
        ArticleRepository $articleRepository
    ): Response
    {
        return $this->render('default/index.html.twig', [
            'articles' => $articleRepository->findBy(['publish' => true], ['publishedAt' => 'DESC'], 3)
        ]);
    }
}
