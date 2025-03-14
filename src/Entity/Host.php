<?php

namespace BlueGrid\Entity;

use BlueGrid\Contract\UuidIdentifiableInterface;
use BlueGrid\Repository\HostRepository;
use BlueGrid\Traits\UuidIdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HostRepository::class)]
class Host implements UuidIdentifiableInterface
{
    use UuidIdentifiableTrait;

    #[ORM\Column(length: 256)]
    private string $name;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Directory $rootDirectory = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRootDirectory(): ?Directory
    {
        return $this->rootDirectory;
    }

    public function setRootDirectory(Directory $rootDirectory): static
    {
        $this->rootDirectory = $rootDirectory;

        return $this;
    }
}
