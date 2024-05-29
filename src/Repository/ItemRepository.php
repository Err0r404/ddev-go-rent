<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\Order;
use App\Model\Finder;
use App\Model\Search;
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
    
    /**
     * @param Search $search
     *
     * @return array<Item>
     */
    public function findItemsUnavailableBySearch(Search $search): array
    {
        $subQuery = $this
            ->itemsUnavailableBetweenDatesQueryBuilder()
            ->getDQL();
        
        $qb = $this->createQueryBuilder('i');
        
        $qb
            ->andWhere($qb->expr()->In('i.id', $subQuery))
            ->setParameter('from', $search->getFromDateTime())
            ->setParameter('to', $search->getToDateTime())
            ->setParameter('status', 'payment_failed');
        ;
        
        return $qb->getQuery()->getResult();
    }
    
    private function itemsUnavailableBetweenDatesQueryBuilder(?Order $orderToExclude = null)
    {
        $randomInt = \random_int(1, PHP_INT_MAX);
        
        $qb = $this
            ->createQueryBuilder("i{$randomInt}")
            ->select("i{$randomInt}.id")
            ->join("i{$randomInt}.lineOrders", "lo{$randomInt}")
            ->join("lo{$randomInt}.order", "o{$randomInt}")
            ->andWhere("
                (:from <= o{$randomInt}.fromDateTime AND :to >= o{$randomInt}.fromDateTime AND :to <= o{$randomInt}.toDateTime)
                OR
                (:from >= o{$randomInt}.fromDateTime AND :from <= o{$randomInt}.toDateTime AND :to >= o{$randomInt}.toDateTime)
                OR
                (:from >= o{$randomInt}.fromDateTime AND :from <= o{$randomInt}.toDateTime AND :to >= o{$randomInt}.fromDateTime AND :to <= o{$randomInt}.toDateTime)
                OR
                (:from <= o{$randomInt}.fromDateTime AND :to >= o{$randomInt}.toDateTime)
                OR
                (:from = o{$randomInt}.fromDateTime AND :to = o{$randomInt}.toDateTime)
            ")
            ->andWhere("o{$randomInt}.status != :status");
        
        if ($orderToExclude) {
            $qb
                ->andWhere("o{$randomInt}.id != :orderToExcludeId");
        }
        
        return $qb;
    }
}
