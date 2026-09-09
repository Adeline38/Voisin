<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

// Indique à Symfony que cette classe sert à fabriquer des objets "Utilisateur" dans la base de données
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]

// Règle de sécurité SQL : Interdit d'avoir deux utilisateurs avec la même adresse email en base de données
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]

// Règle de sécurité SQL : Interdit également d'avoir deux utilisateurs avec le même pseudo
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PSEUDO', fields: ['pseudo'])]

// Barrière de contrôle PHP : Si un visiteur tente de s'inscrire avec un email déjà pris, le formulaire s'arrête et affiche ce message d'erreur poli sans faire planter le site
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte de voisin avec cette adresse email.')]

// Déclaration de l'identité : explique à Symfony que cette classe représente un "Vrai Membre" (UserInterface) capable de se connecter de manière sécurisée avec un mot de passe (PasswordAuthenticatedUserInterface).
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    #[Assert\Email(message: "L'adresse email n'est pas valide (il manque un @ ou l'extension).")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "Le pseudo est obligatoire.")]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    /* #[Assert\File(
        maxSize: '200k',
        extensions: ['jpg', 'png', 'webp'],
        extensionsMessage: 'Veuillez télécharger un fichier valide (Max 200 Ko, JPG / PNG / WEBP.')] */
    private ?string $photo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $biographie = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_inscription = null;

    /**
     * @var Collection<int, Publication>
     */
    #[ORM\OneToMany(targetEntity: Publication::class, mappedBy: 'utilisateur')]
    private Collection $publications;

    /**
     * @var Collection<int, DemandeAmi>
     */
    #[ORM\OneToMany(targetEntity: DemandeAmi::class, mappedBy: 'utilisateur_demandeur')]
    private Collection $demandeAmis;

    /**
     * @var Collection<int, DemandeAmi>
     */
    #[ORM\OneToMany(targetEntity: DemandeAmi::class, mappedBy: 'utilisateur_receveur')]
    private Collection $reponseDemandeAmis;

    /**
     * @var Collection<int, AutorisationAmi>
     */
    #[ORM\OneToMany(targetEntity: AutorisationAmi::class, mappedBy: 'utilisateur')]
    private Collection $autorisationAmis;

    /**
     * @var Collection<int, AutorisationAmi>
     */
    #[ORM\OneToMany(targetEntity: AutorisationAmi::class, mappedBy: 'utilisateur_ami')]
    private Collection $autorisationAmisAcceptee;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'utilisateur')]
    private Collection $commentaires;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'utilisateur')]
    private Collection $likes;

    #[ORM\Column]
    private ?bool $est_en_ligne = null;

    public function __construct()
    {
        $this->publications = new ArrayCollection();
        $this->demandeAmis = new ArrayCollection();
        $this->reponseDemandeAmis = new ArrayCollection();
        $this->autorisationAmis = new ArrayCollection();
        $this->autorisationAmisAcceptee = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->date_inscription = new \DateTimeImmutable();
        $this->est_en_ligne = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);
        
        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): static
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeImmutable
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeImmutable $date_inscription): static
    {
        $this->date_inscription = $date_inscription;

        return $this;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): static
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setUtilisateur($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): static
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getUtilisateur() === $this) {
                $publication->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DemandeAmi>
     */
    public function getDemandeAmis(): Collection
    {
        return $this->demandeAmis;
    }

    public function addDemandeAmi(DemandeAmi $demandeAmi): static
    {
        if (!$this->demandeAmis->contains($demandeAmi)) {
            $this->demandeAmis->add($demandeAmi);
            $demandeAmi->setUtilisateurDemandeur($this);
        }

        return $this;
    }

    public function removeDemandeAmi(DemandeAmi $demandeAmi): static
    {
        if ($this->demandeAmis->removeElement($demandeAmi)) {
            // set the owning side to null (unless already changed)
            if ($demandeAmi->getUtilisateurDemandeur() === $this) {
                $demandeAmi->setUtilisateurDemandeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DemandeAmi>
     */
    public function getReponseDemandeAmis(): Collection
    {
        return $this->reponseDemandeAmis;
    }

    public function addReponseDemandeAmi(DemandeAmi $reponseDemandeAmi): static
    {
        if (!$this->reponseDemandeAmis->contains($reponseDemandeAmi)) {
            $this->reponseDemandeAmis->add($reponseDemandeAmi);
            $reponseDemandeAmi->setUtilisateurReceveur($this);
        }

        return $this;
    }

    public function removeReponseDemandeAmi(DemandeAmi $reponseDemandeAmi): static
    {
        if ($this->reponseDemandeAmis->removeElement($reponseDemandeAmi)) {
            // set the owning side to null (unless already changed)
            if ($reponseDemandeAmi->getUtilisateurReceveur() === $this) {
                $reponseDemandeAmi->setUtilisateurReceveur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AutorisationAmi>
     */
    public function getAutorisationAmis(): Collection
    {
        return $this->autorisationAmis;
    }

    public function addAutorisationAmi(AutorisationAmi $autorisationAmi): static
    {
        if (!$this->autorisationAmis->contains($autorisationAmi)) {
            $this->autorisationAmis->add($autorisationAmi);
            $autorisationAmi->setUtilisateur($this);
        }

        return $this;
    }

    public function removeAutorisationAmi(AutorisationAmi $autorisationAmi): static
    {
        if ($this->autorisationAmis->removeElement($autorisationAmi)) {
            // set the owning side to null (unless already changed)
            if ($autorisationAmi->getUtilisateur() === $this) {
                $autorisationAmi->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AutorisationAmi>
     */
    public function getAutorisationAmisAcceptee(): Collection
    {
        return $this->autorisationAmisAcceptee;
    }

    public function addAutorisationAmisAcceptee(AutorisationAmi $autorisationAmisAcceptee): static
    {
        if (!$this->autorisationAmisAcceptee->contains($autorisationAmisAcceptee)) {
            $this->autorisationAmisAcceptee->add($autorisationAmisAcceptee);
            $autorisationAmisAcceptee->setUtilisateurAmi($this);
        }

        return $this;
    }

    public function removeAutorisationAmisAcceptee(AutorisationAmi $autorisationAmisAcceptee): static
    {
        if ($this->autorisationAmisAcceptee->removeElement($autorisationAmisAcceptee)) {
            // set the owning side to null (unless already changed)
            if ($autorisationAmisAcceptee->getUtilisateurAmi() === $this) {
                $autorisationAmisAcceptee->setUtilisateurAmi(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUtilisateur() === $this) {
                $commentaire->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUtilisateur($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUtilisateur() === $this) {
                $like->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function isEstEnLigne(): ?bool
    {
        return $this->est_en_ligne;
    }

    public function setEstEnLigne(bool $est_en_ligne): static
    {
        $this->est_en_ligne = $est_en_ligne;

        return $this;
    }
}
