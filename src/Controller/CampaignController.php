<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\User;
use App\Enum\CampaignLifecycle;
use App\Form\CampaignType;
use App\Repository\CampaignRepository;
use App\Repository\ClientRepository;
use App\Repository\PlatformRepository;
use App\Repository\UserRepository;
use App\Service\MarkdownRenderer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/campaigns')]
final class CampaignController extends AbstractController
{
    #[Route(name: 'app_campaign_index', methods: ['GET'])]
    public function index(
        Request            $request,
        CampaignRepository $campaignRepository,
        UserRepository     $userRepository,
        ClientRepository   $clientRepository,
        PlatformRepository $platformRepository
    ): Response
    {
        $selectedClient = $request->query->getInt('client', 0);
        $selectedPlatform = $request->query->getInt('platform', 0);
        $selectedProjectManager = $request->query->getInt('project-manager', 0);
        $selectedCampaignOwner = $request->query->getInt('campaign-owner', 0);

        $campaigns = $campaignRepository->filterCampaignsBy($selectedClient, $selectedPlatform, $selectedProjectManager, $selectedCampaignOwner);

        $projectManagers = $userRepository->findByRole('ROLE_PROJECT_MANAGER');
        $campaignOwners = $userRepository->findByRole('ROLE_CAMPAIGN_OWNER');
        $clients = $clientRepository->findAll();
        $platforms = $platformRepository->findAll();

        return $this->render('campaign/index.html.twig', [
            'heading' => 'Aktive Kampagnen',
            'campaigns' => $campaigns,
            'showSelectFilters' => true,

            // Option values for select input fields
            'projectManagers' => $projectManagers,
            'campaignOwners' => $campaignOwners,
            'clients' => $clients,
            'platforms' => $platforms,

            // Selected values by user
            'selectedClient' => $selectedClient,
            'selectedPlatform' => $selectedPlatform,
            'selectedProjectManager' => $selectedProjectManager,
            'selectedCampaignOwner' => $selectedCampaignOwner,
        ]);
    }

    #[Route('/archived', name: 'app_campaigns_archived', methods: ['GET'])]
    public function archived(CampaignRepository $campaignRepository): Response
    {
        return $this->render('campaign/index.html.twig', [
            'heading' => 'Archivierte Kampagnen',
            'campaigns' => $campaignRepository->findAllArchivedCampaigns(),
        ]);
    }

    #[Route('/new', name: 'app_campaign_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campaign = new Campaign();
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaign->setLifecycle(CampaignLifecycle::ACTIVE);

            $entityManager->persist($campaign);
            $entityManager->flush();

            return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campaign/new.html.twig', [
            'campaign' => $campaign,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campaign_show', methods: ['GET'])]
    public function show(Campaign $campaign, MarkdownRenderer $converter): Response
    {
        $campaignDescriptionHTML = (string)$converter->toHtml($campaign->getDescription() ?? '');

        return $this->render('campaign/show.html.twig', [
            'campaign' => $campaign,
            'descriptionHTML' => $campaignDescriptionHTML,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_campaign_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campaign $campaign, EntityManagerInterface $entityManager): Response
    {
        // Only project manager and campaign owner of campaign can edit campaign
        $this->denyAccessUnlessGranted('CAMPAIGN_EDIT', $campaign);

        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campaign/edit.html.twig', [
            'campaign' => $campaign,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/description', name: 'app_campaign_description_update', methods: ['POST'])]
    public function updateCampaignDescription(
        Request                $request,
        Campaign               $campaign,
        EntityManagerInterface $entityManager,
        MarkdownRenderer       $converter
    ): JsonResponse
    {
        // Only project manager and campaign owner of campaign can edit campaign description
        $this->denyAccessUnlessGranted('CAMPAIGN_EDIT', $campaign);

        if (!$this->isCsrfTokenValid('description' . $campaign->getId(), $request->getPayload()->getString('_token'))) {
            return $this->json(['ok' => false, 'error' => 'Invalid CSRF Token.'], Response::HTTP_FORBIDDEN);
        }

        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['ok' => false, 'error' => 'Not authenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        $descriptionMarkdown = $request->request->getString('campaign-description');

        $campaign->setDescription($descriptionMarkdown);
        $campaign->setDescriptionLastEditedBy($user);
        $campaign->setDescriptionUpdatedAt(new \DateTimeImmutable("now"));

        $entityManager->flush();

        return $this->json(
            [
                'ok' => true,
                'description_html' => (string)$converter->toHtml($descriptionMarkdown),
                'description_markdown' => $descriptionMarkdown,
            ],
            Response::HTTP_OK);
    }

    #[Route('/{id}/lifecycle', name: 'app_campaign_toggle_lifecycle', methods: ['POST'])]
    public function toggleCampaignLifecycle(
        Request                $request,
        Campaign               $campaign,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {
        // Only project manager and campaign owner of campaign can archive campaign
        $this->denyAccessUnlessGranted('CAMPAIGN_EDIT', $campaign);

        if (!$this->isCsrfTokenValid('lifecycle' . $campaign->getId(), $request->getPayload()->getString('_token'))) {
            return $this->json(['ok' => false, 'error' => 'Invalid CSRF Token.'], Response::HTTP_FORBIDDEN);
        }

        // 1 => archive current campaign
        // 0 => activate current campaign
        $archiveCampaign = (int)$request->request->get('archive_campaign', 0) === 1;
        $campaign->setLifecycle($archiveCampaign ? CampaignLifecycle::ARCHIVED : CampaignLifecycle::ACTIVE);
        $entityManager->flush();

        return $this->json(
            [
                'ok' => true,
                'lifecycle' => $campaign->getLifecycle()->value,
            ],
            Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_campaign_delete', methods: ['POST'])]
    public function delete(Request $request, Campaign $campaign, EntityManagerInterface $entityManager): Response
    {
        // Only project manager and campaign owner of campaign can delete campaign
        $this->denyAccessUnlessGranted('CAMPAIGN_DELETE', $campaign);

        if ($this->isCsrfTokenValid('delete' . $campaign->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($campaign);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
    }
}
