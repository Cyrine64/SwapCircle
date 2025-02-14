<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
#[ORM\Table(name: "commentaire")]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_commentaire = null;

    #[ORM\ManyToOne(targetEntity: Blog::class)]
    #[ORM\JoinColumn(name: "id_article", referencedColumnName: "id_article", nullable: false)]
    private ?Blog $article = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Objet::class)]
    #[ORM\JoinColumn(name: "id_objet", referencedColumnName: "id_objet", nullable: false)]
    private ?Objet $objet = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $contenu = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_commentaire = null;

    // Getters and setters
    public function getIdCommentaire(): ?int
    {
        return $this->id_commentaire;
    }

    public function getArticle(): ?Blog
    {
        return $this->article;
    }

    public function setArticle(?Blog $article): self
    {
        $this->article = $article;
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

    public function getObjet(): ?Objet
    {
        return $this->objet;
    }

    public function setObjet(?Objet $objet): self
    {
        $this->objet = $objet;
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

    public function getDateCommentaire(): ?\DateTimeInterface
    {
        return $this->date_commentaire;
    }

    public function setDateCommentaire(\DateTimeInterface $date_commentaire): self
    {
        $this->date_commentaire = $date_commentaire;
        return $this;
    }
} 