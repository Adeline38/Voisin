<?php

namespace App\Entity;

use App\Repository\DemandeAmiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeAmiRepository::class)]
class DemandeAmi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_creation = null;

    #[ORM\ManyToOne(inversedBy: 'demandeAmis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur_demandeur = null;

    #[ORM\ManyToOne(inversedBy: 'reponseDemandeAmis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur_receveur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeImmutable $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getUtilisateurDemandeur(): ?Utilisateur
    {
        return $this->utilisateur_demandeur;
    }

    public function setUtilisateurDemandeur(?Utilisateur $utilisateur_demandeur): static
    {
        $this->utilisateur_demandeur = $utilisateur_demandeur;

        return $this;
    }

    public function getUtilisateurReceveur(): ?Utilisateur
    {
        return $this->utilisateur_receveur;
    }

    public function setUtilisateurReceveur(?Utilisateur $utilisateur_receveur): static
    {
        $this->utilisateur_receveur = $utilisateur_receveur;

        return $this;
    }
}
