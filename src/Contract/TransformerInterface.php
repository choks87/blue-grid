<?php
declare(strict_types=1);

namespace BlueGrid\Contract;

interface TransformerInterface
{
    public function transform(mixed $value): mixed;
}