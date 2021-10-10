<?php

namespace App\Controller\Admin\User;

use App\Entity\User\Rank;
use App\Repository\User\RankRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/rank", name="admin_rank_")
 */
class RankController extends AbstractController
{
    /**
     * @param RankRepository $rankRepository
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        RankRepository $rankRepository
    )
    {
        return $this->render('admin/user/rank/index.html.twig', [
            'ranks' => $rankRepository->findAll()
        ]);
    }

    /**
     * @param Rank $rank
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        Rank $rank
    )
    {
        return $this->render('admin/user/rank/show.html.twig', [
            'rank' => $rank
        ]);
    }
}
