<?php

namespace App\Repository\User;

use App\Entity\User\UserPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserPicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPicture[]    findAll()
 * @method UserPicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPicture::class);
    }

    /**
     * @param $userPicture
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeIfInvalidAccount($userPicture)
    {
        $this->_em->remove($userPicture);
        $this->_em->flush();
    }
}
