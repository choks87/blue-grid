<?php

declare(strict_types=1);

namespace BlueGrid\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'api_default')]
    public function __invoke(): JsonResponse
    {
        return $this->json('Welcome to API.');
    }
}
