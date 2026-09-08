<?php

namespace App\Entity;

use App\Repository\AutorisationAmiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AutorisationAmiRepository::class)]
class AutorisationAmi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_autorisation = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationAmis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationAmisAcceptee')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur_ami = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAutorisation(): ?\DateTimeImmutable
    {
        return $this->date_autorisation;
    }

    public function setDateAutorisation(\DateTimeImmutable $date_autorisation): static
    {
        $this->date_autorisation = $date_autorisation;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getUtilisateurAmi(): ?Utilisateur
    {
        return $this->utilisateur_ami;
    }

    public function setUtilisateurAmi(?Utilisateur $utilisateur_ami): static
    {
        $this->utilisateur_ami = $utilisateur_ami;

        return $this;
    }
}
