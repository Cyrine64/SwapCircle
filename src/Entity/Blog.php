<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
#[ORM\Table(name: "blog")]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_article = null;

    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "Le contenu ne peut pas être vide")]
    #[Assert\Length(
        min: 10,
        minMessage: "Le contenu doit contenir au moins {{ limit }} caractères"
    )]
    private ?string $contenu = null;
    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_publication = null;

    #[ORM\Column(type: 'float', options: ['default' => 0])]
    private float $rating = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $ratingCount = 0;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: CommentaireBlog::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: Reaction::class, orphanRemoval: true)]
    private Collection $reactions;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->reactions = new ArrayCollection();
        $this->date_publication = new \DateTime();
    }

    public function getIdArticle(): ?int
    {
        return $this->id_article;
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

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDatePublication(\DateTimeInterface $date_publication): self
    {
        $this->date_publication = $date_publication;
        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

    public function getRatingCount(): int
    {
        return $this->ratingCount;
    }

    public function setRatingCount(int $ratingCount): self
    {
        $this->ratingCount = $ratingCount;
        return $this;
    }

    public function getAverageRating(): float
    {
        return $this->ratingCount > 0 ? $this->rating / $this->ratingCount : 0;
    }

    public function addRating(float $rating): self
    {
        $this->rating += $rating;
        $this->ratingCount++;
        return $this;
    }

    /**
     * @return Collection<int, CommentaireBlog>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(CommentaireBlog $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setBlog($this);
        }

        return $this;
    }

    public function removeCommentaire(CommentaireBlog $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            if ($commentaire->getBlog() === $this) {
                $commentaire->setBlog(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reaction>
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function getLikesCount(): int
    {
        return $this->reactions->filter(fn(Reaction $reaction) => $reaction->getType() === 'like')->count();
    }

    public function getDislikesCount(): int
    {
        return $this->reactions->filter(fn(Reaction $reaction) => $reaction->getType() === 'dislike')->count();
    }
}