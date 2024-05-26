<?php

namespace App\Controller\App;

use App\Form\App\SearchType;
use App\Model\Search;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'app_home')]
    public function index(Request $request, CategoryRepository $categoryRepository): Response
    {
        $search = new Search();
        
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && !$form->isValid()) {
            $search = new Search();
        }
        
        $categories = $categoryRepository->findCategoriesWithAllItems();
        
        return $this->render('app/home/index.html.twig', [
            'form'             => $form->createView(),
            'durationInDays'   => 0,
            'categories'       => $categories,
            'unavailableItems' => [],
        ]);
    }
}
