<?php

namespace App\Model;


use App\DTO\ItemDTO;

class CartItem
{
    private Cart $cart;
    
    private ItemDTO $item;
    
    private float|int $dailyAmount;
    
    public function getCart(): Cart
    {
        return $this->cart;
    }
    
    public function setCart(Cart $cart): CartItem
    {
        $this->cart = $cart;
        return $this;
    }
    
    public function getItem(): ItemDTO
    {
        return $this->item;
    }
    
    public function setItem(ItemDTO $item): CartItem
    {
        $this->item = $item;
        return $this;
    }
    
    public function getDailyAmount(): float|int
    {
        return $this->dailyAmount;
    }
    
    public function setDailyAmount(float|int $dailyAmount): CartItem
    {
        $this->dailyAmount = $dailyAmount;
        return $this;
    }
    
    public function getTotalAmount(): float|int
    {
        return $this->dailyAmount * $this->cart->getDurationInDays();
    }
}