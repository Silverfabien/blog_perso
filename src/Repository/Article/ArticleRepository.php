<?php

namespace App\Repository\Article;

use App\Entity\Article\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save($article)
    {
        $this->_em->persist($article);
        $this->_em->flush();
    }

    public function update($article)
    {
        $this->_em->flush();
    }

    public function remove($article)
    {
        $this->_em->remove($article);
        $this->_em->flush();
    }

    public function see($article)
    {
        $this->_em->flush();
    }
}
