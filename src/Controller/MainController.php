<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CampaignRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_index')]
    public function index(CampaignRepository $campaignRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $campaignsEndingThisMonth = $campaignRepository->campaignsEndingThisMonth();
        $myActiveCampaigns = $campaignRepository->findAllMyActiveCampaigns($user);

        return $this->render('main/index.html.twig', [
            'campaignsEndingThisMonth' => $campaignsEndingThisMonth,
            'myActiveCampaigns' => $myActiveCampaigns,
        ]);
    }
}
