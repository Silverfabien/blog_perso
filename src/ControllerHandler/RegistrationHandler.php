<?php

namespace App\ControllerHandler;

use App\Entity\User\User;
use App\Repository\User\RankRepository;
use App\Repository\User\UserRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationHandler
{
    private $userRepository;
    private $userPasswordEncoder;
    private $rankRepository;
    private $tokenGenerator;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoder,
        RankRepository $rankRepository,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->rankRepository = $rankRepository;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function registerHandle(
        FormInterface $form,
        User $user
    )
    {
        $findRank = $this->rankRepository->findOneByRolename('Utilisateur');

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordEncoder->encodePassword($user, $user->getPassword());
            $request = Request::createFromGlobals();
            $token = $this->tokenGenerator->generateToken();

            $user->setPassword($password);
            $user->getPicture()->setUser($user);
            $user->setRank($findRank);
            $user->setIp($request->getClientIp());
            $user->setConfirmationAccountToken($token);

            $this->userRepository->save($user);

            return true;
        }

        return false;
    }

    public function confirmationAccount(User $user)
    {
        $user->setConfirmationAccountToken(null);
        $user->setConfirmationAccount(true);
        $user->setConfirmationAccountAt(new \DateTimeImmutable());

        $this->userRepository->update($user);

        return true;
    }
}
