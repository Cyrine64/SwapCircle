<?php

namespace App\Entity;

use App\Repository\BlogLikeRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogLikeRepository::class)]
#[ORM\Table(name: "blog_like")]
class BlogLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_blog_like = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "blogLikes")]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Blog::class)]
    #[ORM\JoinColumn(name: "id_article", referencedColumnName: "id_article", nullable: false)]
    private ?Blog $article = null;

    #[ORM\Column(length: 50)]
    private ?string $action = null;

    // Getters and setters
    public function getIdBlogLike(): ?int
    {
        return $this->id_blog_like;
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

    public function getArticle(): ?Blog
    {
        return $this->article;
    }

    public function setArticle(?Blog $article): self
    {
        $this->article = $article;
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }
} 