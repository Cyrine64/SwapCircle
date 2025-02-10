<?php

namespace App\Entity;

use App\Repository\ExchangeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRepository::class)]
class Exchange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_exchange = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_offreur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?Utilisateur $id_offreur = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_demandeur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?Utilisateur $id_demandeur = null;

    #[ORM\ManyToOne(targetEntity: Objet::class)]
    #[ORM\JoinColumn(name: "id_objet_offert", referencedColumnName: "id_object", nullable: false)]
    private ?Objet $id_objet_offert = null;

    #[ORM\ManyToOne(targetEntity: Objet::class)]
    #[ORM\JoinColumn(name: "id_objet_demande", referencedColumnName: "id_object", nullable: false)]
    private ?Objet $id_objet_demande = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_echange = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    public function getIdExchange(): ?int
    {
        return $this->id_exchange;
    }

    public function getIdOffreur(): ?Utilisateur
    {
        return $this->id_offreur;
    }

    public function setIdOffreur(?Utilisateur $id_offreur): self
    {
        $this->id_offreur = $id_offreur;
        return $this;
    }

    public function getIdDemandeur(): ?Utilisateur
    {
        return $this->id_demandeur;
    }

    public function setIdDemandeur(?Utilisateur $id_demandeur): self
    {
        $this->id_demandeur = $id_demandeur;
        return $this;
    }

    public function getIdObjetOffert(): ?Objet
    {
        return $this->id_objet_offert;
    }

    public function setIdObjetOffert(?Objet $id_objet_offert): self
    {
        $this->id_objet_offert = $id_objet_offert;
        return $this;
    }

    public function getIdObjetDemande(): ?Objet
    {
        return $this->id_objet_demande;
    }

    public function setIdObjetDemande(?Objet $id_objet_demande): self
    {
        $this->id_objet_demande = $id_objet_demande;
        return $this;
    }

    public function getDateEchange(): ?\DateTimeInterface
    {
        return $this->date_echange;
    }

    public function setDateEchange(\DateTimeInterface $date_echange): self
    {
        $this->date_echange = $date_echange;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }
} 