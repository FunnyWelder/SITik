<?php

namespace App\Controller;

use App\Service\HealthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/health', name: 'health_')]
class HealthController extends ApiController
{
    #[Route(name: 'show', methods: ['GET'])]
    public function index(HealthService $healthService): JsonResponse
    {
        return $this->response(['APP_ENV' => $healthService->getAppEnv()]);
    }
}
