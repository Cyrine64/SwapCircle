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
    #[ORM\Column]
    private ?int $id_recyclage = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Objet::class)]
    #[ORM\JoinColumn(name: "id_objet", referencedColumnName: "id_objet", nullable: false)]
    private ?Objet $objet = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $type_recyclage = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_recyclage = null;

    #[ORM\Column(type: 'text')]
    private ?string $commentaire = null;

    // Getters and setters
    public function getIdRecyclage(): ?int
    {
        return $this->id_recyclage;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
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

    public function setDateRecyclage(\DateTimeInterface $date_recyclage): self
    {
        $this->date_recyclage = $date_recyclage;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }
} 