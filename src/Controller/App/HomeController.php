<?php

namespace App\Controller\App;

use App\Form\App\SearchType;
use App\Model\Search;
use App\Repository\CategoryRepository;
use App\Repository\ItemRepository;
use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'app_home')]
    public function index(Request $request, CategoryRepository $categoryRepository, ItemRepository $itemRepository, CartManager $cartManager): Response
    {
        $search = new Search();
        
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && !$form->isValid()) {
            $search = new Search();
        }
        
        $categories = $categoryRepository->findCategoriesWithAllItems();
        $unavailableItems = $itemRepository->findItemsUnavailableBySearch($search);
        
        $cart = $cartManager->getCart();
        
        // Initialize Cart dates from Search
        if (!$cart->getFromDateTime() || !$cart->getToDateTime()) {
            $cartManager->setDates($search->getFromDateTime(), $search->getToDateTime());
        }
        
        // Reset Cart dates & items if Search changed
        if ($cart->getFromDateTime() != $search->getFromDateTime() || $cart->getToDateTime() != $search->getToDateTime()) {
            $cartManager->setDates($search->getFromDateTime(), $search->getToDateTime());
            $cartManager->clearItems();
            
            $this->addFlash('success', 'Your cart has been reset');
        }
        
        return $this->render('app/home/index.html.twig', [
            'form'             => $form->createView(),
            'categories'       => $categories,
            'unavailableItems' => $unavailableItems,
            'durationInDays'   => $search->getDurationInDays(),
            'cart'             => $cart,
        ]);
    }
}
