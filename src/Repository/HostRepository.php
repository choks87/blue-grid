<?php
declare(strict_types=1);

namespace BlueGrid\Repository;

use BlueGrid\Criteria\Criteria;
use BlueGrid\Entity\Host;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Host>
 */
class HostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Host::class);
    }

    /**
     * @return Host[]
     */
    public function findAllWithCriteria(Criteria $criteria): array
    {
        $pagination = $criteria->getPagination();

        $queryBuilder = $this->createQueryBuilder('h');
        $queryBuilder
            ->select('h')
            ->orderBy('h.id', 'DESC')
            ->setFirstResult(($pagination->getPage() - 1) * $pagination->getPerPage())
            ->setMaxResults($pagination->getPerPage() + 1);

        /** @var Host[] $results */
        $results = $queryBuilder->getQuery()->getResult();

        return $results;
    }
}
