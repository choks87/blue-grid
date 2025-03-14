<?php
declare(strict_types=1);

namespace BlueGrid\Exception;

use BlueGrid\Enum\TransformType;

class UnsupportedTransformType extends \LogicException
{
    public function __construct(TransformType $type)
    {
        parent::__construct(
            \sprintf('Transform Type "%s" is not supported.', $type->name)
        );
    }
}