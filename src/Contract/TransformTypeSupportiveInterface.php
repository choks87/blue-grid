<?php
declare(strict_types=1);

namespace BlueGrid\Contract;

use BlueGrid\Enum\TransformType;

interface TransformTypeSupportiveInterface extends TransformerInterface
{
    public function supports(TransformType $type): bool;
}