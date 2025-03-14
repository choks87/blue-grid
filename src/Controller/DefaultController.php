<?php

declare(strict_types=1);

namespace BlueGrid\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default')]
    public function __invoke(): JsonResponse
    {
        return $this->json('Welcome to BlueGrid Assessment.');
    }
}
