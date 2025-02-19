<?php

namespace App\Entity;

use App\Repository\ObjetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ObjetRepository::class)]
#[ORM\Table(name: "objet")]
class Objet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_objet = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $etat = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_ajout = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "objets")]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id", nullable: false)]
    private ?Utilisateur $id_utilisateur = null;

    #[ORM\OneToMany(mappedBy: "id_objet", targetEntity: Echange::class)]
    private Collection $echanges;

    #[ORM\OneToMany(mappedBy: "id_objet", targetEntity: Recyclage::class)]
    private Collection $recyclages;

    #[ORM\OneToMany(mappedBy: "id_objet", targetEntity: Commentaire::class)]
    private Collection $commentaires;

    // Getters and setters
    public function getIdObjet(): ?int
    {
        return $this->id_objet;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeInterface $date_ajout): self
    {
        $this->date_ajout = $date_ajout;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getIdUtilisateur(): ?Utilisateur
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur(?Utilisateur $id_utilisateur): self
    {
        $this->id_utilisateur = $id_utilisateur;
        return $this;
    }
} 