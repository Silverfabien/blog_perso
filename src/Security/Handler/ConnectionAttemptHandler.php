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
    const FIRST_BAN = 5;
    const SECOND_BAN = 10;
    const THIRD_BAN = 15;
    const LAST_BAN = 20;

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
    public function connectAttemptHandle(
        User $user
    ): bool
    {
        $user->setConnectionAttempt($user->getConnectionAttempt() +1);
        $connectionAttempt = $user->getConnectionAttempt();

        if (
            $connectionAttempt === self::FIRST_BAN ||
            $connectionAttempt === self::SECOND_BAN ||
            $connectionAttempt === self::THIRD_BAN ||
            $connectionAttempt === self::LAST_BAN
        ) {
            $blocked = new Blocked();
            $blocked->setUser($user);
            $blocked->setBlocked(true);
            $blocked->setBlockedAt(new \DateTimeImmutable());

            switch ($user->getConnectionAttempt()) {
                case self::FIRST_BAN:
                    $blocked->setBlockedReason('Votre compte est bloqué pendant 15 minutes pour trop de tentative de connexion.');
                    $blocked->setUnblockedAt(new \DateTimeImmutable('+15min'));
                    break;
                case self::SECOND_BAN:
                    $blocked->setBlockedReason('Votre compte est bloqué pendant 1 heure pour trop de tentative de connexion.');
                    $blocked->setUnblockedAt(new \DateTimeImmutable('+1hour'));
                    break;
                case self::THIRD_BAN:
                    $blocked->setBlockedReason('Votre compte est bloqué pendant 24 heures pour trop de tentative de connexion.');
                    $blocked->setUnblockedAt(new \DateTimeImmutable('+1day'));
                    break;
                case self::LAST_BAN:
                    $blocked->setBlockedReason('Votre compte est bloqué définitivement pour trop de tentative de connexion.');
                    break;
            }

            $this->blockedRepository->save($blocked);

            return true;
        }

        $this->userRepository->update($user);

        return true;
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetConnectionAttemptHandle(
        User $user
    ): bool
    {
        $blockeds = $this->blockedRepository->findBy(['user' => $user, 'blocked' => true]);
        $user->setConnectionAttempt(0);

        if ($blockeds) {
            foreach ($blockeds as $blocked) {
                $blocked->setBlocked(false);
                $this->blockedRepository->update($blocked);
            }
        }
        $this->userRepository->update($user);

        return true;
    }
}
