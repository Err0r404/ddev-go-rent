<?php

namespace App\Repository;

use App\Entity\Item;
use App\Model\Finder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function findAllQuery(Finder $finder)
    {
        $qb = $this
            ->createQueryBuilder('i')
            ->addSelect('c')
            ->join('i.category', 'c')
            ->andWhere('i.deletedAt IS NULL');

        if ($finder->getSearch()) {
            $qb
                ->andWhere('
                    i.name LIKE :search
                    OR c.name LIKE :search
                ')
                ->setParameter('search', '%' . $finder->getSearch() . '%');
        }

        return $qb->getQuery();
    }
}
