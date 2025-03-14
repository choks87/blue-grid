<?php
declare(strict_types=1);

namespace BlueGrid\Contract;

interface PaginationInterface
{
    public function getPage(): int;

    public function getPerPage(): int;
}