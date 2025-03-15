<?php

declare(strict_types=1);

namespace BlueGrid\Transformer;
;
use BlueGrid\Contract\TransformerInterface;
use BlueGrid\Entity\Directory;
use BlueGrid\Entity\File;

/**
 * @psalm-import-type Files from \BlueGrid\Contract\WebDiskApiInterface
 * @psalm-type Tree = array<Directory>
 */
final class RawToTreeTransformer implements TransformerInterface
{
    /**
     * @param  Files  $value
     * @return Tree
     */
    public function transform(mixed $value): mixed
    {
        $tree = [];

        \sort($value);

        foreach ($value as $url) {
            $parts = \parse_url($url);

            $hostName = $parts['host']; // @phpstan-ignore-line
            $path     = $parts['path']; // @phpstan-ignore-line

            $tree[$hostName] ??= new Directory($hostName);

            $this->applyPathAsDirectories($tree[$hostName], $path);
        }

        return \array_values($tree);
    }

    private function applyPathAsDirectories(Directory $rootDirectory, string $path): void
    {
        $isFilePath = !\str_ends_with($path, '/');
        $segments   = \explode('/', \trim($path, '/'));

        $temp = $rootDirectory;

        foreach ($segments as $segment) {
            if ($isFilePath && $segment === \end($segments)) {
                $temp->addFile(new File($segment));

                return;
            }

            $directory = $temp->findChild($segment);

            if (null === $directory) {
                $directory = new Directory($segment);
                $directory->setParent($temp);
            }

            $temp = $directory;
        }
    }
}