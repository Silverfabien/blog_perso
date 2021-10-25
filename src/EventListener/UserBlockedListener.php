<?php

namespace App\EventListener;

use App\Repository\User\BlockedRepository;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserBlockedListener
 */
class UserBlockedListener
{
    private BlockedRepository $blockedRepository;
    private Security $security;

    /**
     * @param BlockedRepository $blockedRepository
     * @param Security $security
     */
    public function __construct(
        BlockedRepository $blockedRepository,
        Security $security
    )
    {
        $this->blockedRepository = $blockedRepository;
        $this->security = $security;
    }

    /**
     * @param RequestEvent $event
     */
    public function __invoke(
        RequestEvent $event
    ): void
    {
        $currentUser = $this->security->getUser();

        if($currentUser !== null) {
            $userBlocked = $this->blockedRepository->findOneBy(['user' => $currentUser, 'blocked' => true]);

            if ($userBlocked !== null) {
                $this->security->getToken()->setAuthenticated(false);
                throw new CustomUserMessageAuthenticationException('security.account.disabled');
            }
        }
    }
}
