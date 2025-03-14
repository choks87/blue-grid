<?php
declare(strict_types=1);

namespace BlueGrid\Enum;

enum TransformType
{
    case TO_ARRAY;
    case TO_ARRAY_DIRECTORIES_ONLY;
    case TO_ARRAY_FILES_ONLY;
}
