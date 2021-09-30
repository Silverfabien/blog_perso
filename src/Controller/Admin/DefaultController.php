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
    private $articleRepository;
    private $commentRepository;
    private $userRepository;
    private $likeRepository;

    public function __construct(
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        UserRepository $userRepository,
        LikeRepository $likeRepository
    )
    {
        $this->articleRepository = $articleRepository;
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
    }

    /**
     * Page index
     *
     * @return Response
     *
     * @Route("/admin", name="admin_default")
     */
    public function index(): Response
    {
        return $this->render('admin/default/index.html.twig', [
            'nbArticle' => count($this->articleRepository->findAll()),
            'articles' => $this->articleRepository->findAll(),
            'nbComment' => count($this->commentRepository->findAll()),
            'comments' => $this->commentRepository->findAll(),
            'nbUser' => count($this->userRepository->findAll()),
            'users' => $this->userRepository->findAll(),
            'nbLike' => count($this->likeRepository->findAll()),
            'likes' => $this->likeRepository->findAll()
        ]);
    }
}
