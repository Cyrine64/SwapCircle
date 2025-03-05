<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
#[ORM\Table(name: "reponse")]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_reponse = null;

    #[ORM\OneToOne(inversedBy: "reponse", targetEntity: Reclamation::class)]
    #[ORM\JoinColumn(name: "id_reclamation", referencedColumnName: "id", nullable: true, onDelete: "CASCADE")]
    private ?Reclamation $reclamation = null;
    

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'ce champs est obligatoire')]
    #[Assert\Length(
        min: 10,
        minMessage: 'veillez introduire 10 caractères au minimum',)]
    private ?string $contenu = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $date_reponse = null;

    public function getIdReponse(): ?int
    {
        return $this->id_reponse;
    }

    public function setIdReponse(?int $id_reponse): self
    {
        $this->id_reponse = $id_reponse;
        return $this;
    }
    
    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): self
    {
        $this->reclamation = $reclamation;
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

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->date_reponse;
    }

    public function setDateReponse(\DateTimeInterface $date_reponse): self
    {
        $this->date_reponse = $date_reponse;
        return $this;
    }
} 