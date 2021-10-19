<?php

namespace App\Controller;

use App\Repository\Article\ArticleRepository;
use App\Repository\User\UserPictureRepository;
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
    const ID_PICTURE_OWNER = 1;

    /**
     * Page index
     *
     * @return Response
     *
     * @Route("/", name="default")
     */
    public function index(
        ArticleRepository $articleRepository,
        UserPictureRepository $userPictureRepository
    ): Response
    {
        return $this->render('default/index.html.twig', [
            'articles' => $articleRepository->findBy(['publish' => true], ['publishedAt' => 'DESC'], 3),
            'user' => $userPictureRepository->findOneById(self::ID_PICTURE_OWNER)
        ]);
    }
}
