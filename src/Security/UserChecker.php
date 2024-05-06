<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @inheritDoc
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        
        if (!$user->getEmailValidatedAt()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('You have to confirm your account before you can log in.');
        }
        
        if (!$user->isEnabled()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Your account has been deactivated, if you think this is a mistake, please contact an administrator.');
        }
        
        if ($user->getDeletedAt()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Your account has been deleted, if you believe this to be an error, please contact an administrator.');
        }
    }
    
    /**
     * @inheritDoc
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        
        if (!$user->getEmailValidatedAt()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('You will need to confirm your account before you can log in.');
        }
        
        if (!$user->isEnabled()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Your account has been deactivated, if you think this is a mistake, please contact an administrator.');
        }
        
        if ($user->getDeletedAt()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Your account has been deleted, if you believe this to be an error, please contact an administrator.');
        }
    }
}