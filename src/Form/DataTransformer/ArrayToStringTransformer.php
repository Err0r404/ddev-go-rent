<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToStringTransformer implements DataTransformerInterface
{
    /**
     * Transforms an array to a string.
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }
        
        return $value[0];
    }
    
    /**
     * Transforms a string to an array.
     */
    public function reverseTransform(mixed $value): array
    {
        return [$value];
    }
}