<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Enum\CampaignLifecycle;
use App\Form\CampaignType;
use App\Repository\CampaignRepository;
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
    public function index(CampaignRepository $campaignRepository): Response
    {
        return $this->render('campaign/index.html.twig', [
            'heading' => 'Aktive Kampagnen',
            'campaigns' => $campaignRepository->findAllActiveCampaigns(),
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
    public function show(Campaign $campaign): Response
    {
        return $this->render('campaign/show.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_campaign_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campaign $campaign, EntityManagerInterface $entityManager): Response
    {
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

    #[Route('/{id}/lifecycle', name: 'app_campaign_toggle_lifecycle')]
    public function toggleCampaignLifecycle(
        Request                $request,
        Campaign               $campaign,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {
        if (!$this->isCsrfTokenValid('lifecycle' . $campaign->getId(), $request->getPayload()->getString('_token'))) {
            return $this->json(['ok' => false, 'error' => 'Invalid CSRF Token.'], Response::HTTP_FORBIDDEN);
        }

        // 1 => archive current campaign
        // 0 => activate current campaign
        $archiveCampaign = (int)$request->request->get('archive_campaign') === 1;
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
        if ($this->isCsrfTokenValid('delete' . $campaign->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($campaign);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
    }
}
