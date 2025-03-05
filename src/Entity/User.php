<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id_user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Name cannot be empty.")]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $name = null;

    #[ORM\Column(name: "last_name", length: 255)]
    #[Assert\NotBlank(message: "Last name cannot be empty.")]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "Email is required.")]
    #[Assert\Email()]
    private ?string $email = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Password is required.")]
    private ?string $password = null;

    #[ORM\Column(type: "json")]
    private array $role = [];
    // #[ORM\Column(type: 'datetime', nullable: true)]
    // private ?\DateTimeInterface $lastActivity = null;

    // #[ORM\PrePersist]
    // #[ORM\PreUpdate]
    // public function syncRolesAndAdminVerified(): void
    // {
    //     if (in_array('ROLE_ADMIN', $this->role)) {
    //         $this->adminVerified = true;
    //     } else {
    //         $this->adminVerified = false;
    //     }
    // }

    // #[ORM\Column(type: "boolean", options: ["default" => true])]
    // private bool $isEnabled = true;

    // #[ORM\Column(type: 'boolean', options: ['default' => false])]
    // private bool $adminVerified = false;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
      
    }

    public function getId(): ?int
    {
        return $this->id_user;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->role; // Get stored roles
    
        if ($this->adminVerified && !in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN'; // Ensure ROLE_ADMIN is included
        }
    
        return array_unique($roles);
    }public function setRoles(array $roles): self
    {
        $this->role = $roles;
    
        // Ensure admin_verified matches ROLE_ADMIN presence
        $this->admin_verified = in_array('ROLE_ADMIN', $roles);
    
        return $this;
    }

    // public function getLastActivity(): ?\DateTimeInterface
    // {
    //     return $this->lastActivity;
    // }

    // public function setLastActivity(?\DateTimeInterface $lastActivity): self
    // {
    //     $this->lastActivity = $lastActivity;
    //     return $this;
    // }
    // public function getIsEnabled(): bool
    // {
    //     return $this->isEnabled;
    // }

    // public function setIsEnabled(bool $isEnabled): self
    // {
    //     $this->isEnabled = $isEnabled;
    //     return $this;
    // }

    // public function isAdminVerified(): bool
    // {
    //     return $this->adminVerified;
    // }

    // public function setAdminVerified(bool $adminVerified): self
    // {
    //     $this->admin_verified = $adminVerified;
    
    //     if ($adminVerified) {
    //         // Ensure the user has ROLE_ADMIN if verified
    //         if (!in_array('ROLE_ADMIN', $this->role)) {
    //             $this->role[] = 'ROLE_ADMIN';
    //         }
    //     } else {
    //         // Remove ROLE_ADMIN if not verified
    //         $this->role = array_diff($this->role, ['ROLE_ADMIN']);
    //     }
    
    //     return $this;
    // }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
}
