<?php

namespace App\DTO;

use App\Entity\Item;

class ItemDTOFactory
{
    static public function createFromItem(Item $item): ItemDTO
    {
        $itemDTO = new ItemDTO();
        
        $itemDTO->setId($item->getId());
        $itemDTO->setName($item->getName());
        $itemDTO->setDescription($item->getDescription());
        
        $categoryDTO = CategoryDTOFactory::createFromCategory($item->getCategory());
        $itemDTO->setCategory($categoryDTO);
        
        foreach ($item->getPrices() as $price) {
            $priceDTO = PriceDTOFactory::createFromPrice($price);
            $itemDTO->addPrice($priceDTO);
        }
        
        return $itemDTO;
    }
}