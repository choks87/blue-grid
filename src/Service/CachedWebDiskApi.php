<?php

declare(strict_types=1);

namespace BlueGrid\Service;

use BlueGrid\Contract\WebDiskApiInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @psalm-import-type Files from \BlueGrid\Contract\WebDiskApiInterface
 */
final readonly class CachedWebDiskApi implements WebDiskApiInterface
{
    public function __construct(
        private WebDiskApiInterface $webDiskApi,
        private CacheInterface $cache,
        private string $allDataCacheKey,
        private int $cacheTtl,
    ) {
    }

    /**
     * @return Files
     */
    public function getAllPaths(): array
    {

        /** @var Files $value */
        $value = $this->cache->get($this->allDataCacheKey, function (ItemInterface $item) {
            $item->expiresAfter($this->cacheTtl);

            return $this->webDiskApi->getAllPaths();
        });

        return $value;
    }
}