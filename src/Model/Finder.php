<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Finder
{
    #[Assert\Length(min: 3, max: 255)]
    private ?string $search = null;
    
    public function getSearch(): ?string
    {
        return $this->search;
    }
    
    public function setSearch(?string $search): self
    {
        $this->search = $search;
        
        return $this;
    }
}