<?php

namespace App\Entity;

use App\Repository\EchangeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EchangeRepository::class)]
#[ORM\Table(name: "echange")]
class Echange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_echange = null;

    #[ORM\ManyToOne(targetEntity: Objet::class)]
    #[ORM\JoinColumn(name: "id_objet", referencedColumnName: "id_objet", nullable: false)]
    private ?Objet $objet = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $name_echange = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le message est obligatoire')]
    #[Assert\Length(
        min: 10,
        minMessage: 'Le message doit contenir au moins {{ limit }} caractères'
    )]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    private ?string $image_echange = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_echange = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['en_attente', 'accepte', 'refuse'])]
    private ?string $statut = 'en_attente';

    // Getters and setters
    public function getIdEchange(): ?int
    {
        return $this->id_echange;
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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getNameEchange(): ?string
    {
        return $this->name_echange;
    }

    public function setNameEchange(string $name_echange): self
    {
        $this->name_echange = $name_echange;
        return $this;
    }

    public function getImageEchange(): ?string
    {
        return $this->image_echange;
    }

    public function setImageEchange(string $image_echange): self
    {
        $this->image_echange = $image_echange;
        return $this;
    }

    public function getDateEchange(): ?\DateTimeInterface
    {
        return $this->date_echange;
    }

    public function setDateEchange(\DateTimeInterface $date_echange): self
    {
        $this->date_echange = $date_echange;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }
} 