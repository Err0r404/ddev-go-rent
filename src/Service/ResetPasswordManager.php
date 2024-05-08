<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetPasswordManager
{
    use ResetPasswordControllerTrait;
    
    public function __construct(
        private UserRepository $userRepository, private UrlGeneratorInterface $urlGenerator, private MailerManager $mailerManager,
        private TranslatorInterface $translator, private ResetPasswordHelperInterface $resetPasswordHelper, private RequestStack $requestStack,
    )
    {
    }
    
    public function processSendingPasswordResetEmail(string $emailFormData): RedirectResponse
    {
        $url = $this->urlGenerator->generate('app_forgot-password-confirm');
        
        $user = $this
            ->userRepository
            ->findOneBy(
                [
                    'email'     => $emailFormData,
                    'deletedAt' => null,
                ]
            );
        
        // Do not reveal whether a user account was found or not for security reasons.
        if (!$user) {
            return new RedirectResponse($url);
        }
        
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        }
        catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            if($_ENV['APP_ENV'] === 'dev') {
                $this->requestStack->getSession()->getFlashBag()->add(
                    'danger',
                    sprintf('There was a problem handling your password reset request - %s',$e->getReason())
                );
            }
            
            return new RedirectResponse($url);
        }
        
        try {
            $this->mailerManager->sendForgotPasswordEmail($user, $resetToken);
        }
        catch (TransportExceptionInterface $e) {
            $this->requestStack->getSession()->getFlashBag()->add(
                'danger',
                'Une erreur est survenue lors de l\'envoi du mail de réinitialisation de mot de passe, merci de réessayer ultérieurement.'
            );
        }
        
        // Store the token object in session for retrieval in check-email route.
        // $this->setTokenObjectInSession($resetToken);
        $this->requestStack->getSession()->set('ResetPasswordToken', $resetToken);
        
        return new RedirectResponse($url);
    }
    
}