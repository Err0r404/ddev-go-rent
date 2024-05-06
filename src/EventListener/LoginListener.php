<?php

namespace App\EventListener;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use App\Entity\User;

#[AsEventListener(event: 'security.interactive_login', method: 'onSecurityInteractiveLogin')]
class LoginListener
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }
    
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        
        $user->setLastLoggedAt(new \DateTimeImmutable());
        
        $this->manager->persist($user);
        $this->manager->flush();
    }
}