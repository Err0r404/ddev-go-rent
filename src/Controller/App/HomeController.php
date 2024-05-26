<?php

namespace App\Controller\App;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findCategoriesWithAllItems();
        
        return $this->render('app/home/index.html.twig', [
            'durationInDays' => 0,
            'categories' => $categories,
            'unavailableItems' => [],
        ]);
    }
}
