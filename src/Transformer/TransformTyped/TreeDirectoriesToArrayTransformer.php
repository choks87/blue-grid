<?php

declare(strict_types=1);

namespace BlueGrid\Transformer\TransformTyped;

use BlueGrid\Entity\Directory;
use BlueGrid\Enum\TransformType;

/**
 * @psalm-type Tree = array<Directory>
 * @psalm-type TreeAsArray = array<array{string, array<mixed>}>
 */
final class TreeDirectoriesToArrayTransformer extends TreeToArrayTransformer
{
    public function supports(TransformType $type): bool
    {
        return TransformType::TO_ARRAY_DIRECTORIES_ONLY === $type;
    }

    /**
     * @override
     * @return array<string, mixed>
     */
    protected function getNested(Directory $directory): array
    {
        $items = [];

        foreach ($directory->getChildren() as $subDirectory) {
            $items[$subDirectory->getName()] = $this->getNested($subDirectory);
        }

        return $items;
    }


}