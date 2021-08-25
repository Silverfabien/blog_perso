<?php

namespace App\DataFixtures;

use App\Entity\User\Rank;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RankFixtures extends Fixture
{
    const RANK_REFERENCE = "Rank";

    public function load(ObjectManager $manager)
    {
        $rankData = [
            [
                "Administrateur",
                ["ROLE_ADMIN"]
            ],
            [
                "Autheur",
                ["ROLE_AUTHOR"]
            ],
            [
                "Utilisateur",
                ["ROLE_USER"]
            ]
        ];

        $i = 0;

        foreach ($rankData as list($roleName, $role)) {
            $rank = new Rank();
            $rank->setRolename($roleName);
            $rank->setRole($role);

            $manager->persist($rank);
            $manager->flush();

            $this->addReference(self::RANK_REFERENCE.$i, $rank);
            $i++;
        }
    }
}
