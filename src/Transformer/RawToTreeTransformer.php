<?php

declare(strict_types=1);

namespace BlueGrid\Transformer;
;
use BlueGrid\Contract\TransformerInterface;
use BlueGrid\Entity\Directory;
use BlueGrid\Entity\File;
use BlueGrid\Entity\Host;

/**
 * @psalm-import-type Files from \BlueGrid\Contract\WebDiskApiInterface
 * @psalm-type Tree = array<Host>
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

            $tree[$hostName] ??= new Host($hostName);

            $rootDirectory = $this->getRootDirectory($tree[$hostName]);

            $this->applyPathAsDirectories($rootDirectory, $path);
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

            $directory = $temp->getDirectory($segment);

            if (null === $directory) {
                $directory = new Directory($segment);
                $temp->addDirectory($directory);
            }

            $temp = $directory;
        }
    }

    private function getRootDirectory(Host $host): Directory
    {
        if (null === $host->getRootDirectory()) {
            $host->setRootDirectory(new Directory());
        }

        return $host->getRootDirectory(); // @phpstan-ignore-line
    }
}