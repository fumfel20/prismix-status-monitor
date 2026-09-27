<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\StatusServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StatusController extends AbstractController
{
    public function __construct(
        private readonly StatusServiceInterface $statusService,
    ) {
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    #[Route('/statuses', name: 'app_statuses', methods: ['GET'])]
    public function index(): Response
    {
        $statuses = $this->statusService->getStatuses();

        return $this->render('status/index.html.twig', [
            'statuses' => $statuses,
        ]);
    }
}
