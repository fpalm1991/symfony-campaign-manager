<?php

namespace App\Entity;

use App\Enum\CampaignLifecycle;
use App\Enum\CampaignStatus;
use App\Repository\CampaignRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampaignRepository::class)]
class Campaign
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'platform_campaigns')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Platform $platform = null;

    #[ORM\ManyToOne(inversedBy: 'client_campaigns')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column]
    private ?float $budget = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $end_date = null;

    #[ORM\ManyToOne(inversedBy: 'project_manager_campaigns')]
    private ?User $project_manager = null;

    #[ORM\ManyToOne(inversedBy: 'campaign_owner_campaigns')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $campaign_owner = null;

    #[ORM\Column(enumType: CampaignLifecycle::class)]
    private CampaignLifecycle $lifecycle;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    private ?User $description_last_edited_by = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $description_updated_at = null;

    public function __construct()
    {
        $this->setLifecycle(CampaignLifecycle::ACTIVE);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }

    public function setPlatform(?Platform $platform): static
    {
        $this->platform = $platform;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(?\DateTimeImmutable $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeImmutable $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getProjectManager(): ?User
    {
        return $this->project_manager;
    }

    public function setProjectManager(?User $project_manager): static
    {
        $this->project_manager = $project_manager;

        return $this;
    }

    public function getCampaignOwner(): ?User
    {
        return $this->campaign_owner;
    }

    public function setCampaignOwner(?User $campaign_owner): static
    {
        $this->campaign_owner = $campaign_owner;

        return $this;
    }

    public function getLifecycle(): CampaignLifecycle
    {
        return $this->lifecycle;
    }

    public function setLifecycle(CampaignLifecycle $lifecycle): static
    {
        $this->lifecycle = $lifecycle;

        return $this;
    }

    public function getStatus(): CampaignStatus
    {
        $today = new \DateTimeImmutable("today");
        $soon = $today->modify("+7 day");

        $start = $this->getStartDate();
        $end = $this->getEndDate();

        if ($start === null || $end === null) {
            return CampaignStatus::DRAFT;
        }

        if ($today <= $end && $end <= $soon) {
            return CampaignStatus::ENDING_SOON;
        }

        if ($start <= $today && $today <= $end) {
            return CampaignStatus::RUNNING;
        }

        if ($today < $start) {
            return CampaignStatus::PLANNED;
        }

        return CampaignStatus::ENDED;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescriptionLastEditedBy(): ?User
    {
        return $this->description_last_edited_by;
    }

    public function setDescriptionLastEditedBy(?User $description_last_edited_by): static
    {
        $this->description_last_edited_by = $description_last_edited_by;

        return $this;
    }

    public function getDescriptionUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->description_updated_at;
    }

    public function setDescriptionUpdatedAt(?\DateTimeImmutable $description_updated_at): static
    {
        $this->description_updated_at = $description_updated_at;

        return $this;
    }
}
