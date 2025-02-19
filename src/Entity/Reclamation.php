<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\TypeReclamation;
use App\Entity\Utilisateur;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[ORM\Table(name: "reclamation")]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'ce champs est obligatoire')]
    #[Assert\Length(
        min: 10,
        minMessage: 'veillez introduire 10 caractères au minimum',)]
    private ?string $message = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = 'En attente';

    #[ORM\Column(enumType: TypeReclamation::class)]
    private TypeReclamation $type_reclamation;

    #[ORM\Column(type: 'datetime', options :['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $date_reclamation;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'ce champs est obligatoire')]
    #[Assert\Length(
        min: 5,
        minMessage: 'veillez introduire 5 caractères au minimum',)]
    private ?string $titre = null;

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
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

    public function getTypeReclamation(): ?TypeReclamation
    {
        return $this->type_reclamation;
    }

    public function setTypeReclamation(TypeReclamation $type_reclamation): self
    {
        $this->type_reclamation = $type_reclamation;
        return $this;
    }

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->date_reclamation;
    }

    public function setDateReclamation(\DateTimeInterface $date_reclamation): self
    {
        $this->date_reclamation = $date_reclamation;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }
} 
