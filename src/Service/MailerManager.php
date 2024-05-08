<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class MailerManager
{
    public function __construct(private readonly MailerInterface $mailer, private readonly VerifyEmailHelperInterface $verifyEmailHelper)
    {
    }
    
    /**
     * @throws TransportExceptionInterface
     */
    public function sendRegistrationConfirmationEmail(User $user): void
    {
        $signatureComponent = $this->verifyEmailHelper->generateSignature(
            'app_registration_confirmation',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()] // add User's id as an extra query param
        );
        
        $email = (new TemplatedEmail())
            ->from(new Address($_ENV['MAILER_ADDRESS'], $_ENV['MAILER_NAME']))
            ->to(new Address($user->getEmail(), $user->__toString()))
            ->replyTo($_ENV['NO_REPLY_MAILER_ADDRESS'])
            ->subject("You're In! Welcome to {$_ENV['APP_NAME']} 🚀")
            ->htmlTemplate('email/registration_confirmation.html.twig')
            ->context([
                'user' => $user,
                'signedUrl' => $signatureComponent->getSignedUrl(),
            ]);
        
        $this->mailer->send($email);
    }
    
    /**
     * @throws TransportExceptionInterface
     */
    public function sendForgotPasswordEmail(User $user, ResetPasswordToken $resetPasswordToken): void
    {
        $from = new Address($_ENV['MAILER_ADDRESS'], $_ENV['MAILER_NAME']);
        $to   = new Address($user->getEmail(), $user->__toString());
        
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject("Reset Your Password on {$_ENV['APP_NAME']} 🗝️")
            ->htmlTemplate('email/forgot-password.html.twig')
            ->context([
                'user'       => $user,
                'resetToken' => $resetPasswordToken,
            ]);
        
        $this->mailer->send($email);
    }
}