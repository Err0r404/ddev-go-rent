<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserType;
use App\Form\Model\FinderType;
use App\Model\Finder;
use App\Repository\UserRepository;
use App\Service\MailerManager;
use App\Service\Securizer;
use App\Service\UserManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users')]
class UserController extends AbstractController
{
    #[Route('', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $finder = new Finder();
        
        $form = $this->createForm(FinderType::class, $finder);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && !$form->isValid()) {
            $finder->setSearch(null);
        }
        
        $query = $userRepository->findAllQuery($finder);
        
        /** @var User[] $pagination */
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10,
            ['defaultSortFieldName' => 'u.id', 'defaultSortDirection' => 'asc']
        );
        
        return $this->render('admin/user/index.html.twig', [
            'form'       => $form->createView(),
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $passwordHasher, UserManager $userManager, MailerManager $mailerManager): Response
    {
        $user = new User();
        
        $roles = User::ROLES;
        if(!$this->isGranted(User::ROLES['Super Admin'])) {
            unset($roles['Super Admin']);
        }
        
        $form = $this->createForm(UserType::class, $user, ['roles' => $roles]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
            
            $userManager->save($user);
            
            try {
                $mailerManager->sendRegistrationConfirmationEmail($user);
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'An error occurred while sending the confirmation email');
            }
            
            $this->addFlash('success', 'User successfully created');

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, Securizer $securizer, UserPasswordHasherInterface $passwordHasher, UserManager $userManager): Response
    {
        if($user->isDeleted()){
            throw $this->createNotFoundException();
        }
        
        // You must have role 'ROLE_SUPER_ADMIN' to edit a user with role 'ROLE_SUPER_ADMIN'
        if($securizer->isGranted($user, 'ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_SUPER_ADMIN')){
            throw $this->createAccessDeniedException();
        }
        
        $roles = User::ROLES;
        if(!$this->isGranted(User::ROLES['Super Admin'])) {
            unset($roles['Super Admin']);
        }
        
        $form = $this->createForm(UserType::class, $user, ['roles' => $roles]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(null !== $user->getPlainPassword()){
                $user->setPassword($passwordHasher->hashPassword($user, $user->getPlainPassword()));
                $user->eraseCredentials();
            }
            
            $userManager->save($user);
            
            $this->addFlash('success', 'User successfully updated');

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, Securizer $securizer, UserManager $userManager): Response
    {
        if($user->isDeleted()){
            throw $this->createNotFoundException();
        }
        
        // You must have role 'ROLE_SUPER_ADMIN' to edit a user with role 'ROLE_SUPER_ADMIN'
        if($securizer->isGranted($user, 'ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_SUPER_ADMIN')){
            throw $this->createAccessDeniedException();
        }
        
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->get('_token'))) {
            $userManager->remove($user);
            $this->addFlash('success', 'User successfully deleted');
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/send-email-validation', name: 'app_admin_user_send_email_validation', methods: ['POST'])]
    public function sendEmailValidation(Request $request, User $user, Securizer $securizer, MailerManager $mailerManager)
    {
        if($user->isDeleted()){
            throw $this->createNotFoundException();
        }
        
        if ($this->isCsrfTokenValid('sendConfirmation'.$user->getId(), $request->getPayload()->get('_token'))) {
            try {
                $mailerManager->sendRegistrationConfirmationEmail($user);
                $this->addFlash('success', 'Confirmation email sent');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'An error occurred while sending the confirmation email');
            }
        }
        
        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
