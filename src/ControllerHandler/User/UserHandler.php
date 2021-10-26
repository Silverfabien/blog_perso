<?php

namespace App\ControllerHandler\User;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * Class RegistrationHandler
 *
 * Gestion des données à envoyer dans la bdd
 */
class UserHandler
{
    private UserPasswordEncoderInterface $userPasswordEncoder;
    private UserRepository $userRepository;
    private TokenGeneratorInterface $tokenGenerator;

    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userRepository = $userRepository;
        $this->tokenGenerator = $tokenGenerator;
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
            $user->getPicture()->setFilename($user->getPicture()->getPictureFile());

            $this->userRepository->update($user);

            return true;
        }

        return false;
    }

    /**
     * @param FormInterface $form
     * @param User $user
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function editPasswordHandle(
        FormInterface $form,
        User $user
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordEncoder->encodePassword($user, $user->getPassword());

            $user->setPassword($password);
            $user->setUpdatedAt(new DateTimeImmutable());

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
    public function deletedHandle(
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
    public function generateResetTokenHandle(
        User $user
    ): bool
    {
        $token = $this->tokenGenerator->generateToken();

        $user->setResetToken($token);
        $user->setResetTokenCreatedAt(new DateTimeImmutable());
        $user->setResetTokenExpiredAt(new DateTimeImmutable('+1 Hour'));

        $this->userRepository->update($user);

        return true;
    }

    /**
     * @param User $user
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function expiredResetTokenHandle(
        User $user
    ): bool
    {
        $user->setResetToken(null);
        $user->setResetTokenCreatedAt(null);
        $user->setResetTokenExpiredAt(null);

        $this->userRepository->update($user);

        return true;
    }

    /**
     * @param FormInterface $form
     * @param User $user
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function resetPasswordHandle(
        FormInterface $form,
        User $user
    ): bool
    {
        $password = $this->userPasswordEncoder->encodePassword($user, $form->getData()->getPassword());

        $user->setResetToken(null);
        $user->setResetTokenCreatedAt(null);
        $user->setResetTokenExpiredAt(null);
        $user->setResetLastAt(new DateTimeImmutable());
        $user->setUpdatedAt(new DateTimeImmutable());
        $user->setPassword($password);

        $this->userRepository->update($user);

        return true;
    }
}
