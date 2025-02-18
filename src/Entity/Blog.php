<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
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

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: true)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $contenu = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    private ?File $imageFile = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_publication = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Commentaire::class, cascade: ['persist', 'remove'])]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: BlogLike::class, cascade: ['persist', 'remove'])]
    private Collection $likes;

    // Getters and setters
    public function getIdArticle(): ?int
    {
        return $this->id_article;
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

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

     // Getter and setter for the uploaded image file
     public function getImageFile(): ?File
     {
         return $this->imageFile;
     }
 
     public function setImageFile(?File $imageFile = null): void
     {
         $this->imageFile = $imageFile;
 
         // Update the 'image' property with the file's name if a new file is uploaded
         if ($imageFile) {
             $this->image = $imageFile->getClientOriginalName();
         }
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

    public function __construct()
{
    $this->commentaires = new ArrayCollection();
    $this->likes = new ArrayCollection();
    $this->date_publication = new \DateTime();
}

public function getCommentaires(): Collection
{
    return $this->commentaires;
}

public function addCommentaire(Commentaire $commentaire): self
{
    if (!$this->commentaires->contains($commentaire)) {
        $this->commentaires[] = $commentaire;
        $commentaire->setArticle($this);
    }

    return $this;
}

public function removeCommentaire(Commentaire $commentaire): self
{
    if ($this->commentaires->removeElement($commentaire)) {
        // Set the owning side to null (unless already changed)
        if ($commentaire->getArticle() === $this) {
            $commentaire->setArticle(null);
        }
    }

    return $this;
}

public function getLikes(): Collection
{
    return $this->likes;
}

public function getLikeCount(): int
{
    // Filtrer les "likes" dans la collection "likes" où l'action est "like"
    return $this->likes->filter(fn(BlogLike $like) => $like->getAction() === 'like')->count();
}

public function getDislikeCount(): int
{
    // Filtrer les "dislikes" dans la collection "likes" où l'action est "dislike"
    return $this->likes->filter(fn(BlogLike $like) => $like->getAction() === 'dislike')->count();
}


} 