<?php
declare(strict_types=1);

namespace BlueGrid\Tests\Resources;

use BlueGrid\Contract\WebDiskApiInterface;

class MockedWebDiskApi implements WebDiskApiInterface
{
    public function getAllPaths(): array
    {
        static $fakeData;

        if ($fakeData === null) {
            $fakeData = \json_decode(
                \file_get_contents(__DIR__.'/data.json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            $fakeData = \array_map(
                static fn(array $item) => $item['fileUrl'],
                $fakeData['items']
            );
        }

        return $fakeData;
    }
}