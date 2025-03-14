<?php

declare(strict_types=1);

namespace BlueGrid\Controller\Api;

use BlueGrid\Criteria\Criteria;
use BlueGrid\Dto\PaginationDto;
use BlueGrid\Enum\TransformType;
use BlueGrid\Repository\TreeRepository;
use ControlBit\Dto\Attribute\RequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class DirectoriesController extends AbstractController
{
    public function __construct(private readonly TreeRepository $treeRepository)
    {
    }

    #[OA\Tag('Filesystem')]
    #[OA\QueryParameter(
        name       : 'page',
        description: 'Page number, default is 1',
        schema     : new OA\Schema(type: 'integer')
    )]
    #[OA\QueryParameter(
        name       : 'per_page',
        description: 'Hosts per page',
        schema     : new OA\Schema(type: 'integer')
    )]
    #[OA\Response(response: Response::HTTP_OK, description: 'Getting All Directories.')]
    #[Route('/directories', name: 'directories', methods: [Request::METHOD_GET])]
    public function __invoke(#[RequestDto] PaginationDto $paginationDto): JsonResponse
    {
        $criteria = new Criteria(TransformType::TO_ARRAY_DIRECTORIES_ONLY, $paginationDto);

        return $this->json($this->treeRepository->getTree($criteria));
    }
}
