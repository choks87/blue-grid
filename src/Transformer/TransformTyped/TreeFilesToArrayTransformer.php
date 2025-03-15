<?php

declare(strict_types=1);

namespace BlueGrid\Transformer\TransformTyped;

use BlueGrid\Entity\Directory;
use BlueGrid\Enum\TransformType;

/**
 * @psalm-type Tree = array<Directory>
 */
final class TreeFilesToArrayTransformer extends TreeToArrayTransformer
{
    public function supports(TransformType $type): bool
    {
        return TransformType::TO_ARRAY_FILES_ONLY === $type;
    }

    /**
     * @override
     * @return array<int, string>
     */
    protected function getNested(Directory $directory): array
    {
        $items = [];
        foreach ($directory->getChildren() as $subDirectory) {
            $items = \array_merge($items, $this->getNested($subDirectory));

            foreach ($subDirectory->getFiles() as $file) {
                $items[] = $file->getName();
            }
        }

        return $items;
    }
}