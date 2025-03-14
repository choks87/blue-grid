<?php
declare(strict_types=1);

namespace BlueGrid\Entity;

use BlueGrid\Contract\UuidIdentifiableInterface;
use BlueGrid\Traits\UuidIdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Directory implements UuidIdentifiableInterface
{
    use UuidIdentifiableTrait;

    #[ORM\Column(length: 256, nullable: true)]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'directories')]
    private ?self $parent = null;

    /**
     * @var Collection<int, Directory>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Directory::class, cascade: ['persist', 'remove'])]
    private Collection $directories;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(mappedBy: 'directory', targetEntity: File::class, cascade: ['persist', 'remove'])]
    private Collection $files;

    public function __construct(string $name = null)
    {
        $this->directories = new ArrayCollection();
        $this->files       = new ArrayCollection();
        $this->name        = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, Directory>
     */
    public function getDirectories(): Collection
    {
        return $this->directories;
    }

    public function addDirectory(Directory $directory): self
    {
        if (!$this->directories->contains($directory)) {
            $this->directories->add($directory);
            $directory->setParent($this);
        }

        return $this;
    }

    public function getDirectory(string $name): ?Directory
    {
        foreach ($this->directories as $directory) {
            if ($name === $directory->getName()) {
                return $directory;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setDirectory($this);
        }

        return $this;
    }

    public function isRoot(): bool
    {
        return null === $this->parent;
    }
}
