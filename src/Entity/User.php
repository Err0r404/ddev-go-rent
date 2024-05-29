<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeletableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email', repositoryMethod: 'findByEmail')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait, SoftDeletableTrait;
    
    const ROLES = [
        'Super Admin' => 'ROLE_SUPER_ADMIN',
        'Admin'       => 'ROLE_ADMIN',
        'User'        => 'ROLE_USER',
    ];
    
    const ROLES_COLORS = [
        'ROLE_SUPER_ADMIN' => 'danger',
        'ROLE_ADMIN'       => 'warning',
        'ROLE_USER'        => 'primary',
    ];
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private ?string $email = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $lastName = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $firstName = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;
    
    #[Assert\PasswordRequirements]
    private ?string $plainPassword = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $emailValidatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLoggedAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $enabled = true;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class, orphanRemoval: true)]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }
    
    public function __toString(): string
    {
        return $this->getEmail();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    
    public function getRolesLabel(): array
    {
        $roles = $this->getRoles();
        
        // Remove default 'ROLE_USER' value
        if (count($roles) > 1 && ($key = array_search('ROLE_USER', $roles)) !== false) {
            unset($roles[$key]);
        }
        
        $labelRoles = [];
        foreach ($roles as $role) {
            $labelRoles[] = array_search($role, User::ROLES);
        }
        
        return array_unique($labelRoles);
    }
    
    public function getRolesColor(): array
    {
        $roles = $this->getRoles();
        
        // Remove default 'ROLE_USER' value
        if (count($roles) > 1 && ($key = array_search('ROLE_USER', $roles)) !== false) {
            unset($roles[$key]);
        }
        
        $colorRoles = [];
        foreach ($roles as $role) {
            $colorRoles[] = User::ROLES_COLORS[$role];
        }
        
        return array_unique($colorRoles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }
    
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
    
    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        
        return $this;
    }

    public function getEmailValidatedAt(): ?\DateTimeImmutable
    {
        return $this->emailValidatedAt;
    }

    public function setEmailValidatedAt(?\DateTimeImmutable $emailValidatedAt): static
    {
        $this->emailValidatedAt = $emailValidatedAt;

        return $this;
    }

    public function getLastLoggedAt(): ?\DateTimeImmutable
    {
        return $this->lastLoggedAt;
    }

    public function setLastLoggedAt(?\DateTimeImmutable $lastLoggedAt): static
    {
        $this->lastLoggedAt = $lastLoggedAt;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }
}
