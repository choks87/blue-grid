<?php
declare(strict_types=1);

namespace BlueGrid\Repository;

use BlueGrid\Contract\TransformTypeSupportiveInterface;
use BlueGrid\Criteria\Criteria;
use BlueGrid\Dto\PaginatedResult;
use BlueGrid\Exception\UnsupportedTransformType;
use BlueGrid\Service\DataLoader;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * @psalm-import-type TreeAsArray from \BlueGrid\Transformer\TransformTyped\TreeDirectoriesToArrayTransformer
 */
final class TreeRepository
{
    /**
     * @param  iterable<TransformTypeSupportiveInterface>  $transformers
     */
    public function __construct(
        private iterable               $transformers,
        private HostRepository         $hostRepository,
        private TagAwareCacheInterface $cache,
        private DataLoader             $dataLoader,
        private int                    $cacheTtl,
    ) {
    }

    /**
     * @return PaginatedResult<mixed>
     */
    public function getTree(Criteria $criteria): PaginatedResult
    {
        $cacheKey = $this->getCacheKeyBasedOnCriteria($criteria);

        $value = $this->cache->get($cacheKey, function (ItemInterface $item) use ($criteria) {
            $item->tag('data');
            $item->expiresAfter($this->cacheTtl);

            $this->dataLoader->load();

            foreach ($this->transformers as $transformer) {
                if (!$transformer->supports($criteria->getTransformType())) {
                    continue;
                }

                /** @var TreeAsArray $data */
                $data = $transformer->transform(
                    $this->hostRepository->findAllWithCriteria($criteria)
                );

                return new PaginatedResult(
                    $data,
                    $criteria->getPagination(),
                );
            }

            throw new UnsupportedTransformType($criteria->getTransformType());
        });

        return $value; // @phpstan-ignore-line
    }

    private function getCacheKeyBasedOnCriteria(Criteria $criteria): string
    {
        return \sprintf(
            'tree_%s_%s_%s',
            $criteria->getTransformType()->name,
            $criteria->getPagination()->getPage(),
            $criteria->getPagination()->getPerPage()
        );

    }
}
