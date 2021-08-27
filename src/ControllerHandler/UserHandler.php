<?php

namespace App\ControllerHandler;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
}
