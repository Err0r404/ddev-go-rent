<?php

namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Securizer {
    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) {
    }
    
    public function isGranted(UserInterface $user, $attribute, $object = null): bool
    {
        $token = new UsernamePasswordToken($user, 'none', $user->getRoles());
        return ($this->accessDecisionManager->decide($token, [$attribute], $object));
    }
    
}