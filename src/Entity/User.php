<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
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

    /**
     * @var Collection<int, Campaign>
     */
    #[ORM\OneToMany(targetEntity: Campaign::class, mappedBy: 'project_manager')]
    private Collection $project_manager_campaigns;

    /**
     * @var Collection<int, Campaign>
     */
    #[ORM\OneToMany(targetEntity: Campaign::class, mappedBy: 'campaign_owner', orphanRemoval: true)]
    private Collection $campaign_owner_campaigns;

    public function __construct()
    {
        $this->project_manager_campaigns = new ArrayCollection();
        $this->campaign_owner_campaigns = new ArrayCollection();
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
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    /**
     * @return Collection<int, Campaign>
     */
    public function getProjectManagerCampaigns(): Collection
    {
        return $this->project_manager_campaigns;
    }

    public function addProjectManagerCampaign(Campaign $projectManagerCampaign): static
    {
        if (!$this->project_manager_campaigns->contains($projectManagerCampaign)) {
            $this->project_manager_campaigns->add($projectManagerCampaign);
            $projectManagerCampaign->setProjectManager($this);
        }

        return $this;
    }

    public function removeProjectManagerCampaign(Campaign $projectManagerCampaign): static
    {
        if ($this->project_manager_campaigns->removeElement($projectManagerCampaign)) {
            // set the owning side to null (unless already changed)
            if ($projectManagerCampaign->getProjectManager() === $this) {
                $projectManagerCampaign->setProjectManager(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Campaign>
     */
    public function getCampaignOwnerCampaigns(): Collection
    {
        return $this->campaign_owner_campaigns;
    }

    public function addCampaignOwnerCampaign(Campaign $campaignOwnerCampaign): static
    {
        if (!$this->campaign_owner_campaigns->contains($campaignOwnerCampaign)) {
            $this->campaign_owner_campaigns->add($campaignOwnerCampaign);
            $campaignOwnerCampaign->setCampaignOwner($this);
        }

        return $this;
    }

    public function removeCampaignOwnerCampaign(Campaign $campaignOwnerCampaign): static
    {
        if ($this->campaign_owner_campaigns->removeElement($campaignOwnerCampaign)) {
            // set the owning side to null (unless already changed)
            if ($campaignOwnerCampaign->getCampaignOwner() === $this) {
                $campaignOwnerCampaign->setCampaignOwner(null);
            }
        }

        return $this;
    }

    public function __toString(): string {
        return $this->email;
    }
}
