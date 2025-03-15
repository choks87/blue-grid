<?php

declare(strict_types=1);

namespace BlueGrid\Transformer\TransformTyped;

use BlueGrid\Contract\TransformTypeSupportiveInterface;
use BlueGrid\Entity\Directory;
use BlueGrid\Enum\TransformType;

/**
 * @psalm-type Tree = array<Directory>
 */
class TreeToArrayTransformer implements TransformTypeSupportiveInterface
{
    /**
     * @param  array<Directory>  $value
     * @return array<string, mixed>
     */
    public function transform(mixed $value): mixed
    {
        $array = [];
        foreach ($value as $directory) {
            $array[$directory->getName()] = $this->getNested($directory);
        }

        return $array;
    }

    public function supports(TransformType $type): bool
    {
        return TransformType::TO_ARRAY === $type;
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function getNested(Directory $directory): array
    {
        $items = [];

        foreach ($directory->getChildren() as $subDirectory) {
            $items[$subDirectory->getName()] = $this->getNested($subDirectory);
        }

        foreach ($directory->getFiles() as $file) {
            $items[] = $file->getName();
        }

        return $items;
    }
}