<?php

namespace App\Controller\Admin\User;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/connection", name="admin_connection_")
 */
class ConnectionController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        UserRepository $userRepository
    )
    {
        return $this->render('admin/user/connection/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * @param User $user
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        User $user
    )
    {
        return $this->render('admin/user/connection/show.html.twig', [
            'user' => $user
        ]);
    }
}
