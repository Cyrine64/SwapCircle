<?php

namespace App\Entity;

use App\Repository\RecyclageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecyclageRepository::class)]
#[ORM\Table(name: "recyclage")]
class Recyclage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    #[Assert\NotNull(message: "L'utilisateur est requis.")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Objet::class, inversedBy: "recyclages")]
    #[ORM\JoinColumn(name: "id_objet", referencedColumnName: "id_objet", nullable: false)]
    #[Assert\NotNull(message: "L'objet est requis.")]
    private ?Objet $objet = null;

    #[ORM\Column(name: "type_recyclage", length: 50)]
    #[Assert\NotBlank(message: "Le type de recyclage est requis.")]
    private ?string $type_recyclage = null;

    #[ORM\Column(name: "date_recyclage", type: 'datetime')]
    #[Assert\NotNull(message: "La date de recyclage est requise.")]
    private ?\DateTimeInterface $date_recyclage = null;

    #[ORM\Column(name: "commentaire", type: 'text', nullable: true)] 
    private ?string $commentaire = null;

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    
    // Alias method for maintaining compatibility
    public function getUtilisateur(): ?User
    {
        return $this->user;
    }
    
    // Alias method for maintaining compatibility
    public function setUtilisateur(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    
    // Alias method for maintaining compatibility
    public function getIdRecyclage(): ?int
    {
        return $this->id;
    }

    public function getObjet(): ?Objet
    {
        return $this->objet;
    }

    public function setObjet(?Objet $objet): self
    {
        $this->objet = $objet;
        return $this;
    }

    public function getTypeRecyclage(): ?string
    {
        return $this->type_recyclage;
    }

    public function setTypeRecyclage(string $type_recyclage): self
    {
        $this->type_recyclage = $type_recyclage;
        return $this;
    }

    public function getDateRecyclage(): ?\DateTimeInterface
    {
        return $this->date_recyclage;
    }

    public function setDateRecyclage(?\DateTimeInterface $date_recyclage): self
    {
        $this->date_recyclage = $date_recyclage;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }
}
