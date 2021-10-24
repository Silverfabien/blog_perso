<?php

namespace App\ControllerHandler\Admin\User;

use App\Entity\User\Blocked;
use App\Repository\User\BlockedRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class BlockedHandler
{
    private BlockedRepository $blockedRepository;

    public function __construct(
        BlockedRepository $blockedRepository
    )
    {
        $this->blockedRepository = $blockedRepository;
    }

    /**
     * @param FormInterface $form
     * @param Blocked $blocked
     * @param UserInterface $user
     * @return bool
     * @throws Exception
     */
    public function blockedHandle(
        FormInterface $form,
        Blocked $blocked,
        UserInterface $user
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $time = $form->get('time')->getData();

            $blocked->setBlocked(true);
            $blocked->setBlockedAt(new DateTimeImmutable());
            $blocked->setUser($user);

            if ($time) {
                $blocked->setUnblockedAt(new DateTimeImmutable("+".$time));
            }

            $this->blockedRepository->save($blocked);

            return true;
        }

        return false;
    }

    /**
     * @param FormInterface $form
     * @param Blocked $blocked
     * @return bool
     */
    public function unblockedHandle(
        FormInterface $form,
        Blocked $blocked
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $blocked->setBlocked(false);
            $blocked->setUnblockedAt(new DateTimeImmutable());

            $this->blockedRepository->update($blocked);

            return true;
        }

        return false;
    }
}
