<?php

namespace App\Tests;

use App\Entity\Campaign;
use App\Entity\Client;
use App\Entity\Platform;
use App\Entity\User;
use App\Enum\CampaignLifecycle;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = self::getContainer()->get(EntityManagerInterface::class);

        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function test_it_sums_budget_for_active_campaigns_of_a_client(): void
    {
        $platform = new Platform()->setName('Platform 1');
        $clientA = new Client()->setName('Client 1');
        $clientB = new Client()->setName('Client 2');

        $user = new User()
            ->setEmail('User 1')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword('some-random-password');

        $this->em->persist($platform);
        $this->em->persist($clientA);
        $this->em->persist($clientB);
        $this->em->persist($user);

        // Campaigns Client A
        $campaignA1 = new Campaign()
            ->setName('Campaign A 1')
            ->setBudget(100)
            ->setStartDate(new \DateTimeImmutable("now"))
            ->setEndDate(new \DateTimeImmutable("now")->modify("+1 day"))
            ->setPlatform($platform)
            ->setCampaignOwner($user)
            ->setProjectManager($user)
            ->setLifecycle(CampaignLifecycle::ACTIVE);

        $campaignA2 = new Campaign()
            ->setName('Campaign A 2')
            ->setBudget(200)
            ->setStartDate(new \DateTimeImmutable("now"))
            ->setEndDate(new \DateTimeImmutable("now")->modify("+1 day"))
            ->setPlatform($platform)
            ->setCampaignOwner($user)
            ->setProjectManager($user)
            ->setLifecycle(CampaignLifecycle::ACTIVE);

        $campaignA3 = new Campaign()
            ->setName('Campaign A 3')
            ->setBudget(300)
            ->setStartDate(new \DateTimeImmutable("now"))
            ->setEndDate(new \DateTimeImmutable("now")->modify("+1 day"))
            ->setPlatform($platform)
            ->setCampaignOwner($user)
            ->setProjectManager($user)
            ->setLifecycle(CampaignLifecycle::ARCHIVED);

        $clientA->addClientCampaign($campaignA1);
        $clientA->addClientCampaign($campaignA2);
        $clientA->addClientCampaign($campaignA3);

        // Campaigns Client B
        $campaignB1 = new Campaign()
            ->setName('Campaign B 1')
            ->setBudget(2000)
            ->setStartDate(new \DateTimeImmutable("now"))
            ->setEndDate(new \DateTimeImmutable("now")->modify("+1 day"))
            ->setPlatform($platform)
            ->setCampaignOwner($user)
            ->setProjectManager($user)
            ->setLifecycle(CampaignLifecycle::ACTIVE);

        $clientB->addClientCampaign($campaignB1);

        $this->em->persist($campaignA1);
        $this->em->persist($campaignA2);
        $this->em->persist($campaignA3);
        $this->em->persist($campaignB1);

        $this->em->flush();

        $this->assertSame(300.0, $clientA->getActiveBudget());
    }
}
