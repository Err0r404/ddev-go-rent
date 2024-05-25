<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use App\Form\Admin\ItemType;
use App\Form\Model\FinderType;
use App\Model\Finder;
use App\Repository\ItemRepository;
use App\Service\ItemManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/item')]
class ItemController extends AbstractController
{
    #[Route('/', name: 'app_admin_item_index', methods: ['GET'])]
    public function index(Request $request, ItemRepository $itemRepository, PaginatorInterface $paginator): Response
    {
        $finder = new Finder();
        
        $form = $this->createForm(FinderType::class, $finder);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && !$form->isValid()) {
            $finder->setSearch(null);
        }
        
        $query = $itemRepository->findAllQuery($finder);
        
        /** @var Item[] $pagination */
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10,
            ['defaultSortFieldName' => 'i.id', 'defaultSortDirection' => 'asc']
        );
        
        return $this->render('admin/item/index.html.twig', [
            'form'       => $form->createView(),
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_admin_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ItemManager $itemManager): Response
    {
        $item = new Item();
        
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $itemManager->save($item);
            
            $this->addFlash('success', 'Item successfully created');

            return $this->redirectToRoute('app_admin_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/item/new.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_item_show', methods: ['GET'])]
    public function show(Item $item): Response
    {
        if($item->isDeleted()){
            throw $this->createNotFoundException();
        }
        
        return $this->render('admin/item/show.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Item $item, ItemManager $itemManager): Response
    {
        if($item->isDeleted()){
            throw $this->createNotFoundException();
        }
        
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $itemManager->save($item);
            
            $this->addFlash('success', 'Item successfully updated');

            return $this->redirectToRoute('app_admin_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/item/edit.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_item_delete', methods: ['POST'])]
    public function delete(Request $request, Item $item, ItemManager $itemManager): Response
    {
        if($item->isDeleted()){
            throw $this->createNotFoundException();
        }
        
        if ($this->isCsrfTokenValid('delete'.$item->getId(), $request->getPayload()->get('_token'))) {
            $itemManager->remove($item);
            $this->addFlash('success', 'Item successfully deleted');
        }

        return $this->redirectToRoute('app_admin_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
