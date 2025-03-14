<?php
declare(strict_types=1);

namespace BlueGrid\Entity;

use BlueGrid\Contract\UuidIdentifiableInterface;
use BlueGrid\Traits\UuidIdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class File implements UuidIdentifiableInterface
{
    use UuidIdentifiableTrait;

    #[ORM\Column(length: 1024)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Directory::class, inversedBy: 'files')]
    private ?Directory $directory = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $path): static
    {
        $this->name = $path;

        return $this;
    }

    public function getDirectory(): ?Directory
    {
        return $this->directory;
    }

    public function setDirectory(?Directory $directory): File
    {
        $this->directory = $directory;

        return $this;
    }
}
