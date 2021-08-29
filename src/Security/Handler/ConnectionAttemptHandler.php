<?php

namespace App\Security\Handler;

use App\Entity\User\Blocked;
use App\Entity\User\User;
use App\Repository\User\BlockedRepository;
use App\Repository\User\UserRepository;

/**
 * Class ConnectionAttemptHandler
 *
 * Gestion des données à envoyer dans la bdd
 */
class ConnectionAttemptHandler
{
    private $userRepository;
    private $blockedRepository;

    public function __construct(
        UserRepository $userRepository,
        BlockedRepository $blockedRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->blockedRepository = $blockedRepository;
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function connectAttempt(
        User $user
    ): bool
    {
        $connectionAttempt = $user->getConnectionAttempt();

        if (
            $connectionAttempt === 4 ||
            $connectionAttempt === 9 ||
            $connectionAttempt === 14 ||
            $connectionAttempt === 19
        ) {
            $blocked = new Blocked();
            $blocked->setUser($user);
            $blocked->setBlocked(true);
            $blocked->setBlockedAt(new \DateTimeImmutable());
            $blocked->getUser()->setConnectionAttempt($user->getConnectionAttempt() +1);

            switch ($user->getConnectionAttempt()) {
                case 5:
                    $blocked->setBlockedReason('Votre compte est bloqué pendant 15 minutes pour trop de tentative de connexion.');
                    $blocked->setUnblockedAt(new \DateTimeImmutable('+15min'));
                    break;
                case 10:
                    $blocked->setBlockedReason('Votre compte est bloqué pendant 1 heure pour trop de tentative de connexion.');
                    $blocked->setUnblockedAt(new \DateTimeImmutable('+1hour'));
                    break;
                case 15:
                    $blocked->setBlockedReason('Votre compte est bloqué pendant 24 heures pour trop de tentative de connexion.');
                    $blocked->setUnblockedAt(new \DateTimeImmutable('+1day'));
                    break;
                case 20:
                    $blocked->setBlockedReason('Votre compte est bloqué définitivement pour trop de tentative de connexion.');
                    break;
            }

            $this->blockedRepository->save($blocked);

            return true;
        }

        $user->setConnectionAttempt($user->getConnectionAttempt() +1);

        $this->userRepository->update($user);

        return true;
    }
}
