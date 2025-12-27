<?php

namespace App\Controller;

use App\Repository\CampaignRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_index')]
    public function index(CampaignRepository $campaignRepository): Response
    {
        $campaignsEndingThisMonth = $campaignRepository->campaignsEndingThisMonth();

        return $this->render('main/index.html.twig', [
            'campaignsEndingThisMonth' => $campaignsEndingThisMonth,
        ]);
    }
}
