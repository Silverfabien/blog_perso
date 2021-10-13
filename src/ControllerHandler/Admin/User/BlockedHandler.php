<?php

namespace App\ControllerHandler\Admin\User;

use App\Entity\User\Blocked;
use App\Repository\User\BlockedRepository;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class BlockedHandler
{
    private $blockedRepository;

    public function __construct(
        BlockedRepository $blockedRepository
    )
    {
        $this->blockedRepository = $blockedRepository;
    }

    public function blockedHandle(
        FormInterface $form,
        Blocked $blocked,
        UserInterface $user
    )
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $time = $form->get('time')->getData();

            $blocked->setBlocked(true);
            $blocked->setBlockedAt(new \DateTimeImmutable());
            $blocked->setUser($user);

            if ($time) {
                $blocked->setUnblockedAt(new \DateTimeImmutable("+".$time));
            }

            $this->blockedRepository->save($blocked);

            return true;
        }

        return false;
    }

    public function unblockedHandle(
        FormInterface $form,
        Blocked $blocked
    )
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $blocked->setBlocked(false);
            $blocked->setUnblockedAt(new \DateTimeImmutable());

            $this->blockedRepository->update($blocked);

            return true;
        }

        return false;
    }
}
