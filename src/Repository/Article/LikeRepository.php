<?php

namespace App\Repository\Article;

use App\Entity\Article\Article;
use App\Entity\Article\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function unlike($like)
    {
        $this->_em->remove($like);
        $this->_em->flush();
    }

    public function like($like)
    {
        $this->_em->persist($like);
        $this->_em->flush();
    }

    public function mostLike()
    {
        $qb = $this->createQueryBuilder('l')
            ->innerJoin(Article::class, 'a', 'WITH', 'l.article = a.id')
            ->select('COUNT(l.article) as countLike, a')
            ->groupBy('l.article')
            ->orderBy('countLike', 'DESC')
            ->setMaxResults(1)
        ;

        $result = $qb->getQuery()->getSingleResult();

        return $result[0];
    }
}
