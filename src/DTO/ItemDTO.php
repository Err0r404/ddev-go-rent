<?php

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ItemDTO
{
    private ?int $id = null;
    
    private ?string $name = null;
    
    private ?string $description = null;
    
    private ?CategoryDTO $category = null;
    
    private Collection $prices;
    
    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(?int $id): ItemDTO
    {
        $this->id = $id;
        return $this;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function setName(?string $name): ItemDTO
    {
        $this->name = $name;
        return $this;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription(?string $description): ItemDTO
    {
        $this->description = $description;
        return $this;
    }
    
    public function getCategory(): ?CategoryDTO
    {
        return $this->category;
    }
    
    public function setCategory(?CategoryDTO $category): ItemDTO
    {
        $this->category = $category;
        return $this;
    }
    
    /**
     * @return Collection<int, PriceDTO>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }
    
    public function addPrice(PriceDTO $price): static
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            // $price->setItem($this);
        }
        
        return $this;
    }
    
    public function removePrice(PriceDTO $price): static
    {
        if ($this->prices->removeElement($price)) {
            // if ($price->getItem() === $this) {
            //     $price->setItem(null);
            // }
            
            unset($price);
        }
        
        return $this;
    }
}