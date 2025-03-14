<?php
declare(strict_types=1);

namespace BlueGrid\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator as SymfonyUuidGenerator;
use Symfony\Component\Uid\Uuid;

final class UuidGenerator extends AbstractIdGenerator
{
    public function __construct(private readonly SymfonyUuidGenerator $uuidGenerator)
    {
    }

    public function generate(EntityManager $em, $entity)
    {
        return $this->uuidGenerator->generate($em, $entity);
    }

    public function generateId(EntityManagerInterface $em, $entity): mixed
    {
        if (null === $entity) {
            return null;
        }

        $metadata = $em->getClassMetadata(\get_class($entity));
        $value    = $metadata->getSingleIdReflectionProperty()?->getValue($entity); // @phpstan-ignore-line

        if (empty($value)) {
            return $this->uuidGenerator->generateId($em, $entity);
        }

        if ($value instanceof Uuid) {
            return $value;
        }

        throw new \InvalidArgumentException(
            \sprintf("If you manually set UUID it should be instance of %s", Uuid::class)
        );
    }

    public function isPostInsertGenerator(): bool
    {
        return $this->uuidGenerator->isPostInsertGenerator();
    }
}