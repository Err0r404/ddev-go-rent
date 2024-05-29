<?php

namespace App\Service;

use App\DTO\ItemDTOFactory;
use App\Entity\Item;
use App\Model\Cart;
use App\Model\CartItem;
use Symfony\Component\HttpFoundation\RequestStack;

class CartManager
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }
    
    /**
     * Retrieve the Cart from the session
     * @return Cart
     */
    public function getCart(): Cart
    {
        $session = $this->requestStack->getSession();
        
        $cart = $session->get('cart');
        
        if (!$cart) {
            $cart = new Cart();
            $session->set('cart', $cart);
        }
        
        return $cart;
    }
    
    public function saveCart(Cart $cart)
    {
        $session = $this->requestStack->getSession();
        $session->set('cart', $cart);
    }
    
    public function setDates(?\DateTimeImmutable $fromDateTime, ?\DateTimeImmutable $toDateTime): void
    {
        $cart = $this->getCart();
        
        $cart
            ->setFromDateTime($fromDateTime)
            ->setToDateTime($toDateTime);
        
        $this->saveCart($cart);
    }
    
    public function clearItems(): void
    {
        $cart = $this->getCart();
        
        $cart->getCartItems()->clear();
        
        $this->saveCart($cart);
    }
    
    public function addItem(Item $item): void
    {
        $cart = $this->getCart();
        
        $itemDTO = ItemDTOFactory::createFromItem($item);
        
        $dailyAmount = 0;
        foreach ($item->getPrices() as $price) {
            if($cart->getDurationInDays() >= $price->getDuration()){
                $dailyAmount = $price->getAmount();
            }
        }
        
        $cartItem = (new CartItem())
            ->setItem($itemDTO)
            ->setDailyAmount($dailyAmount);
        
        $cart->addCartItem($cartItem);
        
        $this->saveCart($cart);
    }
    
    public function clear(): void
    {
        $cart = $this->getCart();
        
        $cart->getCartItems()->clear();
        
        $this->saveCart($cart);
    }
}