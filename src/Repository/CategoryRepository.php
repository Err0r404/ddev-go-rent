<?php

namespace App\Repository;

use App\Entity\Category;
use App\Model\Finder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findAllQuery(Finder $finder)
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->andWhere('c.deletedAt IS NULL');

        if ($finder->getSearch()) {
            $qb
                ->andWhere('c.name LIKE :search')
                ->setParameter('search', '%' . $finder->getSearch() . '%');
        }

        return $qb
            ->getQuery();
    }
    
    /**
     * @return array<Category>
     */
    public function findCategoriesWithAllItems(): array
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c', 'i')
            ->leftJoin('c.items', 'i')
            ->andWhere('c.deletedAt IS NULL')
            ->andWhere('i.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }
}
