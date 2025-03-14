<?php
declare(strict_types=1);

namespace BlueGrid\Contract;


/**
 * @psalm-type Files = string[]
 *
 * @psalm-type RawFilesData = array{
 *     items: array<array{
 *         fileUrl: string
 *     }>
 * }
 */
interface WebDiskApiInterface
{
    /**
     * @return Files
     */
    public function getAllPaths(): array;
}