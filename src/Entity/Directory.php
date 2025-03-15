<?php
declare(strict_types=1);

namespace BlueGrid\Entity;

use BlueGrid\Contract\UuidIdentifiableInterface;
use BlueGrid\Traits\UuidIdentifiableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * @SuppressWarnings("PHPMD.UnusedPrivateField")
 */
#[Gedmo\Tree(type: 'nested')]
#[ORM\Entity(repositoryClass: NestedTreeRepository::class)]
class Directory implements UuidIdentifiableInterface
{
    use UuidIdentifiableTrait;

    #[ORM\Column(length: 255)]
    private ?string $name;

    #[Gedmo\TreeLeft]
    #[ORM\Column(type: Types::INTEGER)]
    private int $lft; // @phpstan-ignore-line

    #[Gedmo\TreeLevel]
    #[ORM\Column(type: Types::INTEGER)]
    private int $level; // @phpstan-ignore-line

    #[Gedmo\TreeRight]
    #[ORM\Column(type: Types::INTEGER)]
    private int $rgt; // @phpstan-ignore-line

    #[Gedmo\TreeRoot]
    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?self $root; // @phpstan-ignore-line

    #[Gedmo\TreeParent]
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?self $parent; // @phpstan-ignore-line

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['lft' => 'ASC'])]
    private Collection $children;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(mappedBy: 'directory', targetEntity: File::class, cascade: ['persist', 'remove'])]
    private Collection $files;

    public function __construct(string $name = null)
    {
        $this->files    = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->name     = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRoot(): ?self
    {
        return $this->root;
    }

    public function setParent(self $parent): void
    {
        $this->parent = $parent;
        $this->parent->addChild($this);
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function findChild(string $title): ?self
    {
        foreach ($this->children as $child) {
            if ($child->getName() === $title) {
                return $child;
            }
        }

        return null;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
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
}
