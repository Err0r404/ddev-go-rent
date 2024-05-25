<?php

namespace App\Service;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;

class ItemManager
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }
    
    public function save(Item $entity, bool $flush = true): void
    {
        $this->manager->persist($entity);
        
        if ($flush) {
            $this->manager->flush();
        }
    }
    
    public function remove(Item $entity, bool $flush = true): void
    {
        $entity->setDeletedAt(new \DateTimeImmutable());
        
        if ($flush) {
            $this->manager->flush();
        }
    }
}