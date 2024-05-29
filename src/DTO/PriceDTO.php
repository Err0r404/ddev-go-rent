<?php

namespace App\DTO;

class PriceDTO
{
    private ?int $id = null;
    
    private ?float $duration = null;
    
    private ?int $amount = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(?int $id): PriceDto
    {
        $this->id = $id;
        return $this;
    }
    
    public function getDuration(): ?float
    {
        return $this->duration;
    }
    
    public function setDuration(?float $duration): PriceDto
    {
        $this->duration = $duration;
        return $this;
    }
    
    public function getAmount(): ?int
    {
        return $this->amount;
    }
    
    public function setAmount(?int $amount): PriceDto
    {
        $this->amount = $amount;
        return $this;
    }
}