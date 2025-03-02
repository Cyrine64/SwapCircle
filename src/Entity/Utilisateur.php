<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: "utilisateur")]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_utilisateur = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $mot_de_passe = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $role = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_inscription = null;

    #[ORM\OneToMany(mappedBy: "id_utilisateur", targetEntity: Objet::class)]
    private Collection $objets;

    #[ORM\OneToMany(mappedBy: "id_utilisateur", targetEntity: Reclamation::class)]
    private Collection $reclamations;

    #[ORM\OneToMany(mappedBy: "id_utilisateur", targetEntity: Blog::class)]
    private Collection $blogs;

    #[ORM\OneToMany(mappedBy: "id_utilisateur", targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: "id_utilisateur", targetEntity: Reponse::class)]
    private Collection $reponses;

    #[ORM\OneToMany(mappedBy: "id_utilisateur", targetEntity: Recyclage::class)]
    private Collection $recyclages;

    #[ORM\OneToMany(mappedBy: "id_utilisateur", targetEntity: Echange::class)]
    private Collection $echanges;

    #[ORM\OneToMany(mappedBy: "id_utilisateur", targetEntity: Tutorial::class)]
    private Collection $tutorials;

    #[ORM\OneToMany(mappedBy: "id_utilisateur", targetEntity: BlogLike::class)]
    private Collection $blogLikes;

    // Getters and setters
    public function getIdUtilisateur(): ?int
    {
        return $this->id_utilisateur;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): self
    {
        $this->mot_de_passe = $mot_de_passe;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;
        return $this;
    }
}