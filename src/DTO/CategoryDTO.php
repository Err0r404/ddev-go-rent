<?php

namespace App\DTO;

class CategoryDTO
{
    private ?int $id = null;
    
    private ?string $name = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(?int $id): CategoryDTO
    {
        $this->id = $id;
        return $this;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function setName(?string $name): CategoryDTO
    {
        $this->name = $name;
        return $this;
    }
}