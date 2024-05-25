<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Item;
use App\Entity\Price;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    const DEFAULT_PASSWORD = "Password1.";
    
    const SUPER_ADMINS = [
        ["super-admin@ddev-go-rent.ddev.site", "Super", "Admin", self::DEFAULT_PASSWORD],
    ];
    
    const ADMINS = [
        ["admin@ddev-go-rent.ddev.site", "Simple", "Admin", self::DEFAULT_PASSWORD],
    ];
    
    const USERS = [
        ["john.doe@ddev-go-rent.ddev.site", "John", "Doe", self::DEFAULT_PASSWORD],
        ["jane.doe@ddev-go-rent.ddev.site", "Jane", "Doe", self::DEFAULT_PASSWORD],
    ];
    
    const SUPERHEROES = [
        "Marvel" => [
            ["Spider-Man", "Un super-héros agile avec des pouvoirs d'araignée."],
            ["Iron Man", "Tony Stark, un génie milliardaire équipé d'une armure technologique."],
            ["Thor", "Le dieu nordique de la foudre, armé de Mjölnir."],
            ["Hulk", "Bruce Banner, qui se transforme en géant vert lorsqu'il est en colère."],
            ["Black Widow", "Une espionne russe dotée de compétences de combat exceptionnelles."]
        ],
        "DC Comics" => [
            ["Batman", "Bruce Wayne, un justicier masqué doté d'une intelligence exceptionnelle."],
            ["Superman", "Kal-El, un extraterrestre surpuissant venu de Krypton."],
            ["Wonder Woman", "Princesse guerrière amazone avec des pouvoirs divins."],
            ["The Flash", "Barry Allen, un homme qui peut courir à des vitesses surhumaines."],
            ["Aquaman", "Roi d'Atlantis, capable de contrôler les océans."]
        ],
        "Autre" => [
            ["Hellboy", "Un démon invoqué par les nazis, devenu un chasseur de monstres."],
            ["Invincible", "Mark Grayson, un adolescent avec des pouvoirs extraterrestres."],
            ["Tortues Ninja", "Leonardo, Michelangelo, Donatello et Raphael, des tortues mutantes ninjas."]
        ]
    ];
    
    private Faker\Generator $faker;
    
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        $this->faker = Faker\Factory::create("en_US");
        $this->faker->seed('G0-R3NT');
    }
    
    public function load(ObjectManager $manager): void
    {
        $this->loadSuperAdmins($manager);
        $this->loadUsers($manager);
        
        $this->loadCategories($manager);
    }
    
    private function loadSuperAdmins(ObjectManager $manager): void
    {
        echo "Loading super admins...\n";
        
        foreach (self::SUPER_ADMINS as [$email, $firstName, $lastName, $plainPassword]) {
            $user = new User();
            
            // Hash user password
            $password = $this->passwordHasher->hashPassword($user, (string)$plainPassword);
            
            $user
                ->setEmail($email)
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setPassword($password)
                ->setRoles([User::ROLES['Super Admin']])
                ->setEmailValidatedAt(new \DateTimeImmutable())
                ->setEnabled(true)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());
            
            $manager->persist($user);
            
            echo ".";
        }
        
        $manager->flush();
        
        foreach (self::ADMINS as [$email, $firstName, $lastName, $plainPassword]) {
            $user = new User();
            
            // Hash user password
            $password = $this->passwordHasher->hashPassword($user, (string)$plainPassword);
            
            $user
                ->setEmail($email)
                ->setFirstName($firstName)
                ->setLastName($lastName)
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
    
    private function loadUsers(ObjectManager $manager): void
    {
        echo "Loading users...\n";
        
        foreach (self::USERS as [$email, $firstName, $lastName, $plainPassword]) {
            $user = new User();
            
            // Hash user password
            $password = $this->passwordHasher->hashPassword($user, (string)$plainPassword);
            
            $user
                ->setEmail($email)
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setPassword($password)
                ->setRoles([User::ROLES['User']])
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
    
    private function loadCategories(ObjectManager $manager): void
    {
        echo "Loading categories...\n";
        
        foreach (self::SUPERHEROES as $universe => $heroes) {
            $category = (new Category())
                ->setName($universe);
            
            $manager->persist($category);

            foreach ($heroes as [$name, $description]) {
                $item = (new Item())
                    ->setName($name)
                    ->setDescription($description)
                    ->setCategory($category);
                
                $initialAmount = 50000; // 500
                
                $price = (new Price())
                    ->setDuration(0.5)
                    ->setAmount($initialAmount);
                
                $item->addPrice($price);
                
                for ($i = 1; $i <= 5; $i++) {
                    // Decrease price by 10%
                    $initialAmount -= $initialAmount * 0.1;
                    
                    $price = (new Price())
                        ->setDuration($i)
                        ->setAmount($initialAmount);
                    
                    $item->addPrice($price);
                }
                
                $manager->persist($item);
                
                echo ".";
            }

            $manager->flush();
        }
        
        $manager->clear();
        
        echo "\n";
        echo "Done\n";
        echo "\n";
    }
}
