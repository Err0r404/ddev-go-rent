<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Be sure to add the following to your security.yaml file in firewalls > main > form_login:
 * success_handler: App\Security\AuthenticationSuccessHandler
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        $response = new Response();
        
        $response->headers->set('HX-Location', '/');
        $response->headers->set('HX-Refresh', 'true');
        
        return $response;
    }
}