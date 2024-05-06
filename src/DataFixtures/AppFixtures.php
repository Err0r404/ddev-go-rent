<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    const DEFAULT_PASSWORD = "Password1.";
    
    const SUPER_ADMINS = [
        ["super-admin@ddev-starter.ddev.site", self::DEFAULT_PASSWORD],
    ];
    
    const ADMINS = [
        ["admin@ddev-starter.ddev.site", self::DEFAULT_PASSWORD],
    ];
    
    private Faker\Generator $faker;
    
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        $this->faker = Faker\Factory::create("en_US");
        $this->faker->seed('5T4RT3R');
    }
    
    public function load(ObjectManager $manager): void
    {
        $this->loadSuperAdmins($manager);
    }
    
    private function loadSuperAdmins(ObjectManager $manager): void
    {
        echo "Loading super admins...\n";
        
        foreach (self::SUPER_ADMINS as [$email, $plainPassword]) {
            $user = new User();
            
            // Hash user password
            $password = $this->passwordHasher->hashPassword($user, (string)$plainPassword);
            
            $user
                ->setEmail($email)
                ->setPassword($password)
                ->setRoles([User::ROLES['Super Admin']])
                ->setEmailValidatedAt(new \DateTimeImmutable())
                ->setEnabled(true)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());
            
            $manager->persist($user);
            
            echo ".";
        }
        
        foreach (self::ADMINS as [$email, $plainPassword]) {
            $user = new User();
            
            // Hash user password
            $password = $this->passwordHasher->hashPassword($user, (string)$plainPassword);
            
            $user
                ->setEmail($email)
                ->setPassword($password)
                ->setRoles([User::ROLES['Admin']])
                ->setEmailValidatedAt(new \DateTimeImmutable())
                ->setEnabled(true)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());
            
            $manager->persist($user);
            
            echo ".";
        }
        
        $manager->flush();
        $manager->clear();
        
        echo "\n";
        echo "Done\n";
        echo "\n";
    }
    
}
