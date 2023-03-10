<?php

namespace App\Entity;

use App\Repository\OutingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: OutingRepository::class)]
class Outing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Veuillez saisir un nom pour votre sortie')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Minimum {{limit}} caractères s'il vous plait",
        maxMessage: "Maximum {{limit}} caractères s'il vous plait"
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message:'Veuillez saisir une date')]
    #[Assert\GreaterThanOrEqual('now', message: "Date incohérente")]
    private ?\DateTimeInterface $dateTimeStart = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'Veuillez saisir une durée')]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\LessThan(propertyPath: 'dateTimeStart', message: 'La date limite d\'inscription doit être avant la date de la sortie')]
    #[Assert\GreaterThan('now', message: 'La date limite d\'inscription doit être après la date du jour')]
    private ?\DateTimeInterface $registrationLimitDate = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank(message:'Veuillez saisir un nombre de participants')]
    #[Assert\Range(
        notInRangeMessage: 'Il doit y avoir entre {{ min }} et {{ max }} participant(s)',
        min: 1,
        max: 32767,
    )]
    private ?int $nbParticipantsMax = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message:'Veuillez saisir une description')]
    #[Assert\Length(
        min: 3,
        max: 3000,
        minMessage: "Minimum {{limit}} caractères s'il vous plait",
        maxMessage: "Maximum {{limit}} caractères s'il vous plait"
    )]
    private ?string $overview = null;

    #[ORM\ManyToMany(targetEntity: Participant::class, mappedBy: 'outings', cascade: ["remove", "persist"])]
    private Collection $participants;

    #[ORM\ManyToOne(cascade: ["remove", "persist"], inversedBy: 'organizedOutings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $planner = null;

    #[ORM\ManyToOne(cascade: ["remove", "persist"], inversedBy: 'outings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $plannerCampus = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Place $place = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDateTimeStart(): ?\DateTimeInterface
    {
        return $this->dateTimeStart;
    }

    public function setDateTimeStart(\DateTimeInterface $dateTimeStart): self
    {
        $this->dateTimeStart = $dateTimeStart;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRegistrationLimitDate(): ?\DateTimeInterface
    {
        return $this->registrationLimitDate;
    }

    public function setRegistrationLimitDate(\DateTimeInterface $registrationLimitDate): self
    {
        $this->registrationLimitDate = $registrationLimitDate;

        return $this;
    }

    public function getNbParticipantsMax(): ?int
    {
        return $this->nbParticipantsMax;
    }

    public function setNbParticipantsMax(int $nbParticipantsMax): self
    {
        $this->nbParticipantsMax = $nbParticipantsMax;

        return $this;
    }

    public function getOverview(): ?string
    {
        return $this->overview;
    }

    public function setOverview(string $overview): self
    {
        $this->overview = $overview;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addOuting($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeOuting($this);
        }

        return $this;
    }

    public function getPlanner(): ?Participant
    {
        return $this->planner;
    }

    public function setPlanner(?Participant $planner): self
    {
        $this->planner = $planner;

        return $this;
    }

    public function getPlannerCampus(): ?Campus
    {
        return $this->plannerCampus;
    }

    public function setPlannerCampus(?Campus $plannerCampus): self
    {
        $this->plannerCampus = $plannerCampus;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }


}
