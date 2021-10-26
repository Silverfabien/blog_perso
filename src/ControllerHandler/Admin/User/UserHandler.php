<?php

namespace App\ControllerHandler\Admin\User;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormInterface;

class UserHandler
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param FormInterface $form
     * @param User $user
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function editUserHandle(
        FormInterface $form,
        User $user
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new DateTimeImmutable());
            if ($form->get('changePicture')->getData() === false) {
                $user->getPicture()->setFilename($user->getPicture()->getPictureFile());
            }

            $this->userRepository->update($user);

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function removeUserHandle(
        User $user
    ): bool
    {
        $user->setDeleted(true);
        $user->setDeletedAt(new DateTimeImmutable());

        $this->userRepository->update($user);

        return true;
    }

    /**
     * @param User $user
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function unremoveUserHandle(
        User $user
    ): bool
    {
        $user->setDeleted(false);
        $user->setUndeletedAt(new DateTimeImmutable());

        $this->userRepository->update($user);

        return true;
    }

    /**
     * @param User $user
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function removeUserDefinitely(
        User $user
    ): bool
    {
        $user->setRank(null);
        $user->setEmail("");
        $user->setPassword("");
        $user->setFirstname("");
        $user->setLastname("deleted".uniqid(0,0));
        $user->setIp("");
        $user->setDeletedDefinitely(true);
        $user->setDeletedDefinitelyAt(new DateTimeImmutable());
        $user->getPicture()->setFilename("");

        $this->userRepository->update($user);

        return true;
    }
}
