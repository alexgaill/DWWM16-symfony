<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findLorem()
    {
        $qb = $this->createQueryBuilder('a');

        $qb->select(['a.title', 'a.content'])
            ->orWhere($qb->expr()->like('a.title', $qb->expr()->literal('% voluptas %')))
            ->orWhere("a.content LIKE '% voluptas %'")
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(5)
        ;
        return $qb->getQuery()->getResult();
    }

    /**
     * Retourne les 5 derniers articles contenant le mot voluptas
     *
     * @return array<Article>
     */
    public function findVoluptas(): array
    {
        return $this->createQueryBuilder('a')
        ->select(['a.title', 'a.content'])
        ->orWhere("a.title LIKE '% voluptas %'")
        ->orWhere("a.content LIKE '% voluptas %'")
        ->orderBy('a.id', 'ASC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult()
        ;
    }
}
