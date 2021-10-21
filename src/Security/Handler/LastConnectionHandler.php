<?php

namespace App\Security\Handler;

use App\Entity\User\User;
use App\Repository\User\UserRepository;

class LastConnectionHandler
{
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function lastConnection(
        User $user
    )
    {
        $user->setLastConnectedAt(new \DateTimeImmutable());

        $this->userRepository->update($user);

        return true;
    }
}
