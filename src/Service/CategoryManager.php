<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryManager
{
    public function __construct(private readonly EntityManagerInterface $manager, private readonly ItemManager $itemManager)
    {
    }
    
    public function save(Category $entity, bool $flush = true): void
    {
        $this->manager->persist($entity);
        
        if ($flush) {
            $this->manager->flush();
        }
    }
    
    public function remove(Category $entity, bool $flush = true): void
    {
        $entity->setDeletedAt(new \DateTimeImmutable());
        
        foreach ($entity->getItems() as $item) {
            $this->itemManager->remove($item, false);
        }
        
        if ($flush) {
            $this->manager->flush();
        }
    }
}