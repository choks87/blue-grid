<?php
declare(strict_types=1);

namespace BlueGrid\Service;

use BlueGrid\Contract\WebDiskApiInterface;
use BlueGrid\Entity\Directory;
use BlueGrid\Entity\File;
use BlueGrid\Entity\Host;
use BlueGrid\Transformer\RawToTreeTransformer;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class DataLoader
{
    public function __construct(
        private WebDiskApiInterface    $webDiskApi,
        private EntityManagerInterface $entityManager,
        private RawToTreeTransformer   $treeTransformer,
        private TagAwareCacheInterface $cache,
        private LoggerInterface        $logger,
        private int                    $batchSize,
        private string                 $environment,
    ) {
    }

    public function load(): void
    {
        $paths = $this->webDiskApi->getAllPaths();

        /** @var array<Host> $tree */
        $tree = $this->treeTransformer->transform($paths);

        /*
            I could add transaction after this line, but other than having some
            data passed, it wouldn't matter much as truncate would happen after
            calling this method.

            This have to be, however, omitted in test, as DAMA already uses transaction

            @see https://dev.mysql.com/doc/refman/8.4/en/truncate-table.html
        */

        $this->truncateDatabaseData();

        try {
            for ($i = 1, $iMax = \count($tree); $i <= $iMax; $i++) {
                $this->entityManager->persist($tree[$i - 1]);

                if ($i % $this->batchSize === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
            }

            $this->entityManager->flush();
            $this->entityManager->clear();

            $this->logger->info('External Data successfully loaded.');

        } catch (\Exception $e) {
            $this->logger->error(\sprintf('Failed to Load External Data. Reason: %s', $e->getMessage()));
        }

        $this->cache->invalidateTags(['data']);
    }

    private function truncateDatabaseData(): void
    {
        if ('test' === $this->environment) {
            return;
        }

        $this->truncateTableForEntity(File::class);
        $this->truncateTableForEntity(Directory::class);
        $this->truncateTableForEntity(Host::class);
    }

    private function truncateTableForEntity(string $entityFqcn): void
    {
        $classMetaData = $this->entityManager->getClassMetadata($entityFqcn);

        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $connection->executeQuery($dbPlatform->getTruncateTableSql($classMetaData->getTableName()));
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
    }
}