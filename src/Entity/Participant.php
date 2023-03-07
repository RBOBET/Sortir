<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?bool $isEnabled = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\ManyToMany(targetEntity: Outing::class, inversedBy: 'participants')]
    private Collection $outings;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\OneToMany(mappedBy: 'planner', targetEntity: Outing::class)]
    private Collection $organizedOutings;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pseudo = null;

    public function __construct()
    {
        $this->outings = new ArrayCollection();
        $this->organizedOutings = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function isIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue() : void {
        $this->setIsEnabled(true);
    }

    /**
     * @return Collection<int, Outing>
     */
    public function getOutings(): Collection
    {
        return $this->outings;
    }

    public function addOuting(Outing $outing): self
    {
        if (!$this->outings->contains($outing)) {
            $this->outings->add($outing);
        }

        return $this;
    }

    public function removeOuting(Outing $outing): self
    {
        $this->outings->removeElement($outing);

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Outing>
     */
    public function getOrganizedOutings(): Collection
    {
        return $this->organizedOutings;
    }

    public function addOrganizedOuting(Outing $organizedOuting): self
    {
        if (!$this->organizedOutings->contains($organizedOuting)) {
            $this->organizedOutings->add($organizedOuting);
            $organizedOuting->setPlanner($this);
        }

        return $this;
    }

    public function removeOrganizedOuting(Outing $organizedOuting): self
    {
        if ($this->organizedOutings->removeElement($organizedOuting)) {
            // set the owning side to null (unless already changed)
            if ($organizedOuting->getPlanner() === $this) {
                $organizedOuting->setPlanner(null);
            }
        }

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }


}
