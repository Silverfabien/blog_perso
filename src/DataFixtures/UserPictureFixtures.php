<?php

namespace App\DataFixtures;

use App\Entity\User\UserPicture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserPictureFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $userPictureData = [
            [
                "",
                $this->getReference(UserFixtures::USER_REFERENCE.'0')
            ],
            [
                "",
                $this->getReference(UserFixtures::USER_REFERENCE.'1')
            ],
            [
                "",
                $this->getReference(UserFixtures::USER_REFERENCE.'2')
            ]
        ];

        foreach ($userPictureData as list($filename, $user)) {
            $userPicture = new UserPicture();
            $userPicture->setFilename($filename);
            $userPicture->setUser($user);

            $manager->persist($userPicture);
            $manager->flush();
        }
    }

    public function getDepedencies()
    {
        return [UserFixtures::class];
    }
}
