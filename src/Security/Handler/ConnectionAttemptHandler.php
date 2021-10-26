<?php

namespace App\Security\Handler;

use App\Entity\User\Blocked;
use App\Entity\User\User;
use App\Repository\User\BlockedRepository;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Class ConnectionAttemptHandler
 *
 * Gestion des données à envoyer dans la bdd
 */
class ConnectionAttemptHandler
{
    protected const FIRST_BAN = 5;
    protected const SECOND_BAN = 10;
    protected const THIRD_BAN = 15;
    protected const LAST_BAN = 20;

    private UserRepository $userRepository;
    private BlockedRepository $blockedRepository;

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
     * @throws ORMException
     * @throws OptimisticLockException
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
            $blocked->setBlockedAt(new DateTimeImmutable());

            switch ($user->getConnectionAttempt()) {
                case self::FIRST_BAN:
                    $blocked->setBlockedReason('Votre compte est bloqué pendant 15 minutes pour trop de tentative de connexion.');
                    $blocked->setUnblockedAt(new DateTimeImmutable('+15min'));
                    break;
                case self::SECOND_BAN:
                    $blocked->setBlockedReason('Votre compte est bloqué pendant 1 heure pour trop de tentative de connexion.');
                    $blocked->setUnblockedAt(new DateTimeImmutable('+1hour'));
                    break;
                case self::THIRD_BAN:
                    $blocked->setBlockedReason('Votre compte est bloqué pendant 24 heures pour trop de tentative de connexion.');
                    $blocked->setUnblockedAt(new DateTimeImmutable('+1day'));
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
     * @throws ORMException
     * @throws OptimisticLockException
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
