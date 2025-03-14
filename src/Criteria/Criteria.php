<?php
declare(strict_types=1);

namespace BlueGrid\Criteria;

use BlueGrid\Contract\PaginationCriteriaInterface;
use BlueGrid\Dto\PaginationDto;
use BlueGrid\Enum\TransformType;

final readonly class Criteria implements PaginationCriteriaInterface
{
    public function __construct(private TransformType $transformType, private PaginationDto $pagination)
    {
    }

    public function getPagination(): PaginationDto
    {
        return $this->pagination;
    }

    public function getTransformType(): TransformType
    {
        return $this->transformType;
    }
}