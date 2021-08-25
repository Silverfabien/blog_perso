<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserFixtures extends Fixture
{
    const USER_REFERENCE = "USER";

    private $passwordEncoder;
    private $request;
    private $tokenGenerator;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->request = $request;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function load(ObjectManager $manager)
    {
        $userData = [
            [
                "admin@gmail.com",
                "Admin",
                "Admin",
                "123456789",
                "127.0.0.1",
                $this->tokenGenerator->generateToken(),
                $this->getReference(RankFixtures::RANK_REFERENCE.'0')
            ],
            [
                "author@gmail.com",
                "Autheur",
                "Autheur",
                "123456789",
                "",
                $this->tokenGenerator->generateToken(),
                $this->getReference(RankFixtures::RANK_REFERENCE.'1')
            ],
            [
                "user@gmail.com",
                "User",
                "User",
                "123456789",
                "127.0.0.1",
                $this->tokenGenerator->generateToken(),
                $this->getReference(RankFixtures::RANK_REFERENCE.'2')
            ]
        ];

        $i = 0;

        foreach ($userData as list($email, $firstname, $lastname, $password, $ip, $confirmationAccountToken, $rank)) {
            $user = new User();
            $user->setEmail($email);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setIp($ip);
            $user->setConfirmationAccountToken($confirmationAccountToken);
            $user->setRank($rank);

            $manager->persist($user);
            $manager->flush();

            $this->addReference(self::USER_REFERENCE.$i, $user);
            $i++;
        }
    }

    public function getDepedencies()
    {
        return [RankFixtures::class];
    }
}
