<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\Security\ForgotPasswordType;
use App\Form\Security\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\ResetPasswordManager;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;
    
    public function __construct(private UserRepository $userRepository, private ResetPasswordHelperInterface $resetPasswordHelper)
    {
    }
    
    #[Route('/forgot-password', name: 'app_forgot-password')]
    public function forgotPassword(Request $request, ResetPasswordManager $resetPasswordManager): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            return $resetPasswordManager->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
            );
        }
        
        return $this->render('security/reset_password/forgot-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/forgot-password/confirm', name: 'app_forgot-password-confirm')]
    public function forgotPasswordConfirm()
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }
        
        return $this->render('security/reset_password/forgot-password-confirm.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }
    
    #[Route('/reset-password', name: 'app_reset-password')]
    public function resetPassword(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, UserManager $userManager): Response
    {
        if ($request->query->has('token')) {
            // Token is stored in session and removed from the URL, to avoid the URL being loaded in a browser and
            // potentially leaked to 3rd party JavaScript.
            $this->storeTokenInSession($request->query->get('token'));
            return $this->redirectToRoute('app_reset-password');
        }
        
        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('Token is missing from the URL or the session.');
        }
        
        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        }
        catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('danger', 'An error occurred : '. $e->getReason());
            return $this->redirectToRoute('app_forgot-password');
        }
        
        // The token is valid here; allow the user to change their password.
        
        $form = $this->createForm(ResetPasswordType::class, $user, []);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);
            
            // Encode(hash) the plain password, and set it.
            $user->setPassword($userPasswordHasherInterface->hashPassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
            
            $userManager->save($user);
            
            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();
            
            $this->addFlash('success', [
                'Your password has been reset successfully.',
                'You can now log in.',
            ]);
            
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('security/reset_password/reset-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
