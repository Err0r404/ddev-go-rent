<?php

namespace App\DTO;

use App\Entity\Price;

class PriceDTOFactory
{
    static public function createFromPrice(Price $price): PriceDTO
    {
        $priceDTO = new PriceDTO();
        
        $priceDTO->setId($price->getId());
        $priceDTO->setDuration($price->getDuration());
        $priceDTO->setAmount($price->getAmount());
        
        return $priceDTO;
    }
}