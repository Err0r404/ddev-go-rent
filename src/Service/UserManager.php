<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }
    
    public function save(User $entity, bool $flush = true): void
    {
        $this->manager->persist($entity);
        
        if ($flush) {
            $this->manager->flush();
        }
    }
    
    public function remove(User $entity, bool $flush = true): void
    {
        $entity
            ->setEmail($entity->getEmail() . '-deleted-' . date('Ymd-His'))
            ->setDeletedAt(new \DateTimeImmutable());
        
        if ($flush) {
            $this->manager->flush();
        }
    }
}