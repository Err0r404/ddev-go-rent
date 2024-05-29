<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Cart
{
    private ?\DateTimeImmutable $fromDateTime;
    
    private ?\DateTimeImmutable $toDateTime;
    
    private Collection $cartItems;
    
    public function __construct()
    {
        $this->fromDateTime = null;
        $this->toDateTime   = null;
        $this->cartItems    = new ArrayCollection();
    }
    
    public function getFromDateTime(): ?\DateTimeImmutable
    {
        return $this->fromDateTime;
    }
    
    public function setFromDateTime(?\DateTimeImmutable $fromDateTime): Cart
    {
        $this->fromDateTime = $fromDateTime;
        return $this;
    }
    
    public function getToDateTime(): ?\DateTimeImmutable
    {
        return $this->toDateTime;
    }
    
    public function setToDateTime(?\DateTimeImmutable $toDateTime): Cart
    {
        $this->toDateTime = $toDateTime;
        return $this;
    }
    
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }
    
    public function setCartItems(Collection $cartItems): Cart
    {
        $this->cartItems = $cartItems;
        return $this;
    }
    
    public function addCartItem(CartItem $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setCart($this);
        }
        
        return $this;
    }
    
    public function removeCartItem(CartItem $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getCart() === $this) {
                unset($cartItem);
            }
        }
        
        return $this;
    }
    
    public function getDurationInDays(): float|int
    {
        // 12 hours = 0.5 days
        // 24 hours = 1 day
        // 36 hours = 1.5 days
        return $this->getFromDateTime()->diff($this->getToDateTime())->d + ($this->getFromDateTime()->diff($this->getToDateTime())->h / 12);
    }
    
    public function getTotalAmount(): float|int
    {
        $total = 0;
        
        /** @var CartItem $cartItem */
        foreach ($this->cartItems as $cartItem) {
            $total += $cartItem->getTotalAmount();
        }
        
        return $total;
    }
}