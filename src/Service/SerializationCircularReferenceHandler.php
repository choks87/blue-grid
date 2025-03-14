<?php

declare(strict_types=1);

namespace BlueGrid\Service;


use BlueGrid\Contract\UuidIdentifiableInterface;

class SerializationCircularReferenceHandler
{
    /**
     * @param  UuidIdentifiableInterface  $object
     */
    public function __invoke(mixed $object): string
    {
        return $object->getId()->toRfc4122(); // @phpstan-ignore-line
    }
}
