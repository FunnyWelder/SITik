<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test', name: 'test_')]
class TestController extends ApiController
{
    #[Route(name: 'show', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->respondWithSuccess('Testing');
    }
}