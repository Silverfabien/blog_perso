<?php

namespace App\Controller\Admin\Statistic;

use App\Repository\Article\ArticleRepository;
use App\Repository\Article\CommentRepository;
use App\Repository\Article\LikeRepository;
use App\Repository\Article\TagsRepository;
use App\Repository\Contact\ContactRepository;
use App\Repository\User\BlockedRepository;
use App\Repository\User\RankRepository;
use App\Repository\User\UserPictureRepository;
use App\Repository\User\UserRepository;
use App\Repository\Visitor\VisitorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/statistic", name="admin_statistic_")
 */
class StatisticController extends AbstractController
{
    /**
     * @param ArticleRepository $articleRepository
     * @param CommentRepository $commentRepository
     * @param LikeRepository $likeRepository
     * @param TagsRepository $tagsRepository
     * @param ContactRepository $contactRepository
     * @param UserRepository $userRepository
     * @param BlockedRepository $blockedRepository
     * @param RankRepository $rankRepository
     * @param UserPictureRepository $userPictureRepository
     * @param VisitorRepository $visitorRepository
     * @return Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        LikeRepository $likeRepository,
        TagsRepository $tagsRepository,
        ContactRepository $contactRepository,
        UserRepository $userRepository,
        BlockedRepository $blockedRepository,
        RankRepository $rankRepository,
        UserPictureRepository $userPictureRepository,
        VisitorRepository $visitorRepository
    ): Response
    {
        return $this->render('admin/statistic/index.html.twig', [
            'articles' => $articleRepository->findAll(),
            'users' => $userRepository->findAll(),
            'userPictures' => $userPictureRepository->findAll(),
            'visitors' => $visitorRepository->findAll(),
            'nbArticle' => count($articleRepository->findAll()),
            'nbComment' => count($commentRepository->findAll()),
            'nbLike' => count($likeRepository->findAll()),
            'nbTag' => count($tagsRepository->findAll()),
            'nbContact' => count($contactRepository->findAll()),
            'nbContactTrue' => count($contactRepository->findBy(['confirm' => true])),
            'nbContactFalse' => count($contactRepository->findBy(['confirm' => false])),
            'nbUser' => count($userRepository->findAll()),
            'nbUserEmailConfirm' => count($userRepository->findBy(['confirmationAccount' => true])),
            'nbUserEmailNotConfirm' => count($userRepository->findBy(['confirmationAccount' => false])),
            'nbUserDeleted' => count($userRepository->findBy(['deleted' => true])),
            'nbUserBlocked' => count($blockedRepository->findBy(['blocked' => true])),
            'nbRank' => count($rankRepository->findAll()),
            'nbUserVisitor' => count($visitorRepository->findAll()),
            'mostLike' => $likeRepository->mostLike(),
            'mostPopular' => $articleRepository->findOneBy([], ['see' => 'DESC'])
        ]);
    }
}
