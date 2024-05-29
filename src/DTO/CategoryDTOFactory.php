<?php

namespace App\DTO;

use App\Entity\Category;

class CategoryDTOFactory
{
    static public function createFromCategory(Category $category): CategoryDTO
    {
        $categoryDto = new CategoryDTO();
        
        $categoryDto->setId($category->getId());
        $categoryDto->setName($category->getName());
        
        return $categoryDto;
    }
}