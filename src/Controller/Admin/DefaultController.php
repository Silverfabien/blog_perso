<?php

namespace App\Controller\Admin;

use App\Repository\Article\ArticleRepository;
use App\Repository\Article\CommentRepository;
use App\Repository\Article\LikeRepository;
use App\Repository\User\UserRepository;
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
     * @param ArticleRepository $articleRepository
     * @param CommentRepository $commentRepository
     * @param UserRepository $userRepository
     * @param LikeRepository $likeRepository
     * @return Response
     *
     * @Route("/admin", name="admin_default")
     */
    public function index(
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        UserRepository $userRepository,
        LikeRepository $likeRepository
    ): Response
    {
        return $this->render('admin/default/index.html.twig', [
            'nbArticle' => count($articleRepository->findAll()),
            'articles' => $articleRepository->findAll(),
            'nbComment' => count($commentRepository->findAll()),
            'comments' => $commentRepository->findAll(),
            'nbUser' => count($userRepository->findAll()),
            'users' => $userRepository->findAll(),
            'nbLike' => count($likeRepository->findAll()),
            'likes' => $likeRepository->findAll()
        ]);
    }
}
