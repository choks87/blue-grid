<?php
declare(strict_types=1);

namespace BlueGrid\Dto;

use BlueGrid\Contract\PaginationInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PaginationDto implements PaginationInterface
{
    #[Assert\Type('integer')]
    private int $page;

    #[Assert\Type('integer')]
    #[Assert\Range(max: 100)]
    private int $perPage;

    public function __construct(int $page = 1, int $perPage = 100)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }
}