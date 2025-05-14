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
    #[ORM\Column(name: "id", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "name", length: 255)]
    #[Assert\NotBlank(message: "Name cannot be empty.")]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $name = null;

    #[ORM\Column(name: "last_name", length: 255)]
    #[Assert\NotBlank(message: "Last name cannot be empty.")]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $lastName = null;

    #[ORM\Column(name: "email", length: 255, unique: true)]
    #[Assert\NotBlank(message: "Email is required.")]
    #[Assert\Email()]
    private ?string $email = null;

    #[ORM\Column(name: "password", type: "string", length: 255)]
    #[Assert\NotBlank(message: "Password is required.")]
    private ?string $password = null;
    
    #[ORM\Column(name: "role", type: "string", length: 50)]
    private string $role = '["ROLE_USER"]';
    
    #[ORM\Column(name: "last_activity", type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastActivity = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function syncRolesAndAdminVerified(): void
    {
        if (in_array('ROLE_ADMIN', $this->getRoles())) {
            $this->adminVerified = true;
        } else {
            $this->adminVerified = false;
        }
    }

    #[ORM\Column(name: "is_enabled", type: "boolean", options: ["default" => true])]
    private bool $isEnabled = true;

    #[ORM\Column(name: "admin_verified", type: 'boolean', options: ['default' => false])]
    private bool $adminVerified = false;
    
    #[ORM\Column(name: "reset_token", type: "string", length: 255, nullable: true)]
    private ?string $resetToken = null;

    public function __construct()
    {
        $this->role = '["ROLE_USER"]';
    }

    public function getId(): ?int
    {
        return $this->id;
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
        if ($this->role === 'admin') {
            return ['ROLE_ADMIN', 'ROLE_USER'];
        }
        
        try {
            $roles = json_decode($this->role, true) ?: [];
            if (!is_array($roles)) {
                return ['ROLE_USER'];
            }
            return $roles;
        } catch (\Exception $e) {
            return ['ROLE_USER'];
        }
    }
    
    public function setRoles(array $roles): self
    {
        if (in_array('ROLE_ADMIN', $roles)) {
            $this->role = 'admin';
        } else {
            $this->role = json_encode($roles);
        }
        
        $this->adminVerified = in_array('ROLE_ADMIN', $roles);
        return $this;
    }

    public function getLastActivity(): ?\DateTimeInterface
    {
        return $this->lastActivity;
    }

    public function setLastActivity(?\DateTimeInterface $lastActivity): self
    {
        $this->lastActivity = $lastActivity;
        return $this;
    }
    
    public function getIsEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    public function isAdminVerified(): bool
    {
        return $this->adminVerified;
    }

    public function setAdminVerified(bool $adminVerified): self
    {
        $this->adminVerified = $adminVerified;
        
        if ($adminVerified) {
            $this->role = 'admin';
        } elseif ($this->role === 'admin') {
            $this->role = '["ROLE_USER"]';
        }
        
        return $this;
    }
    
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }
    
    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
}
