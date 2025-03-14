<?php
declare(strict_types=1);

namespace BlueGrid\Dto;

use BlueGrid\Contract\PaginationInterface;

/**
 * @psalm-template T
 */
final readonly class PaginatedResult implements PaginationInterface
{
    /**
     * @var array<T>
     */
    private array               $data;
    private bool                $hasMore;
    private PaginationInterface $pagination;

    /**
     * @param  array<T>  $data
     */
    public function __construct(array $data, PaginationInterface $pagination)
    {
        $this->pagination = $pagination;
        $this->hasMore    = $pagination->getPerPage() < \count($data);
        $this->data       = \array_slice($data, 0, $pagination->getPerPage());
    }

    /**
     * @return array<T>
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getPage(): int
    {
        return $this->pagination->getPage();
    }

    public function getPerPage(): int
    {
        return $this->pagination->getPerPage();
    }

    public function hasMore(): bool
    {
        return $this->hasMore;
    }
}