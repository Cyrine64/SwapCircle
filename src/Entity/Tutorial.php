<?php

namespace App\Entity;

use App\Repository\TutorialRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TutorialRepository::class)]
#[ORM\Table(name: "tutoriel")]
class Tutorial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_tutorial")]
    private ?int $id_tutorial = null;

    #[ORM\Column(name: "description", type: 'text')]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(name: "vid_url", length: 255)]
    #[Assert\NotBlank]
    private ?string $vid_URL = null;

    #[ORM\Column(name: "date_creation", type: 'datetime')]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne(targetEntity: Recyclage::class)]
    #[ORM\JoinColumn(name: "id_recyclage", referencedColumnName: "id", nullable: false)]
    private ?Recyclage $recyclage = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user = null;

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

    public function getRecyclage(): ?Recyclage
    {
        return $this->recyclage;
    }

    public function setRecyclage(?Recyclage $recyclage): self
    {
        $this->recyclage = $recyclage;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
