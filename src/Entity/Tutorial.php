<?php

namespace App\Entity;

use App\Repository\TutorialRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TutorialRepository::class)]
#[ORM\Table(name: "tutorial")]
class Tutorial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_tutorial = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $vid_URL = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne(targetEntity: Recyclage::class)]
    #[ORM\JoinColumn(name: "id_recyclage", referencedColumnName: "id_recyclage", nullable: false)]
    private ?Recyclage $recyclage = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    // Getters and setters
    public function getIdTutorial(): ?int
    {
        return $this->id_tutorial;
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

    public function getVidURL(): ?string
    {
        return $this->vid_URL;
    }

    public function setVidURL(string $vid_URL): self
    {
        $this->vid_URL = $vid_URL;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }
} 