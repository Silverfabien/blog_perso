<?php

namespace App\Security\Handler;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class LastConnectionHandler
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function lastConnection(
        User $user
    ): bool
    {
        $user->setLastConnectedAt(new DateTimeImmutable());

        $this->userRepository->update($user);

        return true;
    }
}
