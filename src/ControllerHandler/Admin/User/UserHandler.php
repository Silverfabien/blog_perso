<?php

namespace App\ControllerHandler\Admin\User;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Symfony\Component\Form\FormInterface;

class UserHandler
{
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function editUserHandle(
        FormInterface $form,
        User $user
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTimeImmutable());
            if ($form->get('changePicture')->getData() === false) {
                $user->getPicture()->setFilename($user->getPicture()->getPictureFile());
            }

            $this->userRepository->update($user);

            return true;
        }

        return false;
    }

    public function removeUserHandle(
        User $user
    )
    {
        $user->setDeleted(true);
        $user->setDeletedAt(new \DateTimeImmutable());

        $this->userRepository->update($user);

        return true;
    }

    public function unremoveUserHandle(
        User $user
    )
    {
        $user->setDeleted(false);
        $user->setUndeletedAt(new \DateTimeImmutable());

        $this->userRepository->update($user);

        return true;
    }

    public function removeUserDefinitely(
        User $user
    )
    {
        $user->setRank(null);
        $user->setEmail("");
        $user->setPassword("");
        $user->setFirstname("");
        $user->setLastname("deleted".uniqid(0,0));
        $user->setIp("");
        $user->setDeletedDefinitely(true);
        $user->setDeletedDefinitelyAt(new \DateTimeImmutable());
        $user->getPicture()->setFilename("");

        $this->userRepository->update($user);

        return true;
    }
}
