<?php

namespace App\Entity;

use App\Repository\PlatformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatformRepository::class)]
class Platform
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Campaign>
     */
    #[ORM\OneToMany(targetEntity: Campaign::class, mappedBy: 'platform', orphanRemoval: true)]
    private Collection $platform_campaigns;

    public function __construct()
    {
        $this->platform_campaigns = new ArrayCollection();
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

    /**
     * @return Collection<int, Campaign>
     */
    public function getPlatformCampaigns(): Collection
    {
        return $this->platform_campaigns;
    }

    public function addPlatformCampaign(Campaign $platformCampaign): static
    {
        if (!$this->platform_campaigns->contains($platformCampaign)) {
            $this->platform_campaigns->add($platformCampaign);
            $platformCampaign->setPlatform($this);
        }

        return $this;
    }

    public function removePlatformCampaign(Campaign $platformCampaign): static
    {
        if ($this->platform_campaigns->removeElement($platformCampaign)) {
            // set the owning side to null (unless already changed)
            if ($platformCampaign->getPlatform() === $this) {
                $platformCampaign->setPlatform(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
