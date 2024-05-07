<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\Security\RegisterType;
use App\Repository\UserRepository;
use App\Service\MailerManager;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, UserManager $userManager, MailerManager $mailerManager): Response
    {
        $user = new User();
        
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPlainPassword()));
            
            $user->eraseCredentials();
            
            $userManager->save($user);
            
            try {
                $mailerManager->sendRegistrationConfirmationEmail($user);
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', [
                    'Oops! An error occurred while sending the email',
                    'Please try again later or contact our support.'
                ]);
            }
            
            $this->addFlash('success', [
                'Your account has been successfully created',
                'Check your inbox to activate your account before logging in.'
            ]);
            
            $response = new RedirectResponse($this->generateUrl('app_login'));
            
            $response->headers->set('HX-Location', $response->getTargetUrl());
            $response->headers->set('HX-Refresh', 'true');
            
            return $response;
        }
        
        return $this->render('security/register/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/registration-confirmation', name: 'app_registration_confirmation')]
    public function confirmation(Request $request, UserRepository $userRepository, VerifyEmailHelperInterface $verifyEmailHelper, UserManager $userManager): Response
    {
        $id = $request->get('id'); // retrieve User's id from the url
        
        // Verify User id exists and is not null
        if (null === $id) {
            if ($_ENV['APP_ENV'] === 'dev'){
                $this->addFlash('danger', 'User id is missing');
            }
            
            return $this->redirectToRoute('app_home');
        }
        
        // Retrieve User by id
        $user = $userRepository
            ->findOneBy([
                'id'        => $id,
                'deletedAt' => null,
            ]);
        
        // Ensure the user exists in persistence
        if (null === $user) {
            if ($_ENV['APP_ENV'] === 'dev'){
                $this->addFlash('danger', 'User with id "'.$id.'" not found');
            }
            
            return $this->redirectToRoute('app_home');
        }
        
        try {
            $verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        }
        catch (VerifyEmailExceptionInterface $exception) {
            if($_ENV['APP_ENV'] === 'dev'){
                $this->addFlash('danger', $exception->getReason());
            }
            
            return $this->redirectToRoute('app_home');
        }
        
        // Mark user as verified
        $user->setEmailValidatedAt(new \DateTimeImmutable());
        
        $userManager->save($user);
        
        $this->addFlash('success', [
            'Your account has been successfully activated.',
            'You can now log in.'
        ]);
        
        return $this->redirectToRoute('app_login');
    }
    
}
