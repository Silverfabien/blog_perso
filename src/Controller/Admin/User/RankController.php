<?php

namespace App\Controller\Admin\User;

use App\Entity\User\Rank;
use App\Repository\User\RankRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RankController
 *
 * @Route("/admin/rank", name="admin_rank_")
 */
class RankController extends AbstractController
{
    /**
     * @param RankRepository $rankRepository
     * @return Response
     *
     *  @Route("/", name="index", methods={"GET"})
     */
    public function index(
        RankRepository $rankRepository
    ): Response
    {
        return $this->render('admin/user/rank/index.html.twig', [
            'ranks' => $rankRepository->findAll()
        ]);
    }

    /**
     * @param Rank $rank
     * @return Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        Rank $rank
    ): Response
    {
        return $this->render('admin/user/rank/show.html.twig', [
            'rank' => $rank
        ]);
    }
}
