<?php

namespace BlueGrid\Contract;

use BlueGrid\Dto\PaginationDto;

interface PaginationCriteriaInterface
{
    public function getPagination(): PaginationDto;
}