<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\Admin\CategoryType;
use App\Form\Model\FinderType;
use App\Model\Finder;
use App\Repository\CategoryRepository;
use App\Service\CategoryManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_admin_category_index', methods: ['GET'])]
    public function index(Request $request, CategoryRepository $categoryRepository, PaginatorInterface $paginator): Response
    {
        $finder = new Finder();
        
        $form = $this->createForm(FinderType::class, $finder);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && !$form->isValid()) {
            $finder->setSearch(null);
        }
        
        $query = $categoryRepository->findAllQuery($finder);
        
        /** @var Category[] $pagination */
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10,
            ['defaultSortFieldName' => 'c.id', 'defaultSortDirection' => 'asc']
        );
        
        return $this->render('admin/category/index.html.twig', [
            'form'       => $form->createView(),
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_admin_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryManager $categoryManager): Response
    {
        $category = new Category();
        
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryManager->save($category);
            
            $this->addFlash('success', 'Category successfully created');

            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        if($category->isDeleted()){
            throw $this->createNotFoundException();
        }
        
        return $this->render('admin/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryManager $categoryManager): Response
    {
        if($category->isDeleted()){
            throw $this->createNotFoundException();
        }
        
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryManager->save($category);
            
            $this->addFlash('success', 'Category successfully updated');

            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryManager $categoryManager): Response
    {
        if($category->isDeleted()){
            throw $this->createNotFoundException();
        }
        
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->getPayload()->get('_token'))) {
            $categoryManager->remove($category);
            $this->addFlash('success', 'Category successfully deleted');
        }

        return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
