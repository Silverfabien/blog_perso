<?php

namespace App\Repository\User;

use App\Entity\User\Blocked;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Blocked|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blocked|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blocked[]    findAll()
 * @method Blocked[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blocked::class);
    }

    public function save($blocked)
    {
        $this->_em->persist($blocked);
        $this->_em->flush();
    }
}
