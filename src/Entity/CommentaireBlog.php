<?php

namespace App\Entity;

use App\Repository\CommentaireBlogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentaireBlogRepository::class)]
class CommentaireBlog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le contenu ne peut pas être vide")]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datecommentaire = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(name: 'blog_id', referencedColumnName: 'id_article', nullable: false)]
    private ?Blog $blog = null;

    public function __construct()
    {
        $this->datecommentaire = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDatecommentaire(): ?\DateTimeInterface
    {
        return $this->datecommentaire;
    }

    public function setDatecommentaire(?\DateTimeInterface $datecommentaire): static
    {
        $this->datecommentaire = $datecommentaire;
        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): static
    {
        $this->blog = $blog;
        return $this;
    }
}
