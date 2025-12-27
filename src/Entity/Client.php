<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domain = null;

    /**
     * @var Collection<int, Campaign>
     */
    #[ORM\OneToMany(targetEntity: Campaign::class, mappedBy: 'client', orphanRemoval: true)]
    private Collection $client_campaigns;

    public function __construct()
    {
        $this->client_campaigns = new ArrayCollection();
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

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Collection<int, Campaign>
     */
    public function getClientCampaigns(): Collection
    {
        return $this->client_campaigns;
    }

    public function addClientCampaign(Campaign $clientCampaign): static
    {
        if (!$this->client_campaigns->contains($clientCampaign)) {
            $this->client_campaigns->add($clientCampaign);
            $clientCampaign->setClient($this);
        }

        return $this;
    }

    public function removeClientCampaign(Campaign $clientCampaign): static
    {
        if ($this->client_campaigns->removeElement($clientCampaign)) {
            // set the owning side to null (unless already changed)
            if ($clientCampaign->getClient() === $this) {
                $clientCampaign->setClient(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
