<?php

namespace App\Controller\App;

use App\Entity\Item;
use App\Model\CartItem;
use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    public function __construct(private readonly CartManager $cartManager)
    {
    }
    
    public function navbar(): Response
    {
        return $this->render('app/cart/navbar.html.twig', [
            'cart' => $this->cartManager->getCart(),
        ]);
    }
    
    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clear(Request $request): Response
    {
        // Clear CartItems from Cart
        $this->cartManager->clear();
        
        // Redirect to referrer
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
    
    #[Route('/cart/item/{id}/add', name: 'app_cart_add_item')]
    public function addItem(Item $item, Request $request): Response
    {
        if($item->isDeleted()){
            throw $this->createNotFoundException('Item does not exist or no longer exists');
        }
        
        // Get Cart from Session
        $cart = $this->cartManager->getCart();
        
        // Find CartItem in Cart with Item
        $cartItem = $cart->getCartItems()->filter(fn (CartItem $cartItem) => $cartItem->getItem()->getId() === $item->getId())->first();
        
        // If CartItem not found, create new CartItem and add it to Cart
        if (!$cartItem) {
            $this->cartManager->addItem($item);
        }
        
        // Redirect to referrer
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
    
    #[Route('/cart/item/{id}/remove', name: 'app_cart_remove_item')]
    public function remove(Item $item, Request $request): Response
    {
        // Get Cart from Session
        $cart = $this->cartManager->getCart();
        
        // Find CartItem in Cart with Item
        $cartItem = $cart->getCartItems()->filter(fn (CartItem $cartItem) => $cartItem->getItem()->getId() === $item->getId())->first();
        
        // If CartItem found, remove it from Cart
        if ($cartItem) {
            $cart->removeCartItem($cartItem);
            $this->cartManager->saveCart($cart);
        }
        
        // Redirect to referrer
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
    
}
