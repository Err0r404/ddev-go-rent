<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait SoftDeletableTrait
{
    #[ORM\Column(nullable: true)]
    protected ?\DateTimeImmutable $deletedAt;
    
    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }
    
    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;
        
        return $this;
    }
    
    public function isDeleted(): bool
    {
        return $this->deletedAt instanceof \DateTimeInterface;
    }
}
