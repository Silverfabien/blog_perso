<?php

namespace App\Controller\Admin\User;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConnectionController
 *
 * @Route("/admin/connection", name="admin_connection_")
 */
class ConnectionController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @return Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        UserRepository $userRepository
    ): Response
    {
        return $this->render('admin/user/connection/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * @param User $user
     * @return Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        User $user
    ): Response
    {
        return $this->render('admin/user/connection/show.html.twig', [
            'user' => $user
        ]);
    }
}
