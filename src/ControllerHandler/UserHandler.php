<?php

namespace App\ControllerHandler;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationHandler
 *
 * Gestion des données à envoyer dans la bdd
 */
class UserHandler
{
    private $userPasswordEncoder;
    private $userRepository;

    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserRepository $userRepository
    )
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userRepository = $userRepository;
    }

    public function editUserHandle(
        FormInterface $form,
        User $user
    )
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTimeImmutable());
            $user->getPicture()->setFilename($user->getPicture()->getPictureFile());

            $this->userRepository->update($user);

            return true;
        }

        return false;
    }

    public function editPasswordHandle(
        FormInterface $form,
        User $user
    )
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordEncoder->encodePassword($user, $user->getPassword());

            $user->setPassword($password);
            $user->setUpdatedAt(new \DateTimeImmutable());

            $this->userRepository->update($user);

            return true;
        }

        return false;
    }

    public function deletedHandle(
        User $user
    )
    {
        $user->setDeleted(true);
        $user->setDeletedAt(new \DateTimeImmutable());

        $this->userRepository->update($user);

        return true;
    }
}
