<?php

namespace Jinya\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseEntity;
use Jinya\Entity\Base\HistoryEnabledEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="folder")
 */
class Folder extends HistoryEnabledEntity
{
    use BaseEntity;

    /**
     * @var Folder
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\Folder", inversedBy="childFolders")
     */
    private $parent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Media\Folder", mappedBy="parent")
     */
    private $childFolders;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Media\File", mappedBy="folder")
     */
    private $files;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Jinya\Entity\Media\Tag", mappedBy="folders")
     */
    private $tags;

    /**
     * Folder constructor.
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return Folder
     */
    public function getParent(): Folder
    {
        return $this->parent;
    }

    /**
     * @param Folder $parent
     */
    public function setParent(Folder $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildFolders(): ArrayCollection
    {
        return $this->childFolders;
    }

    /**
     * @param ArrayCollection $childFolders
     */
    public function setChildFolders(ArrayCollection $childFolders): void
    {
        $this->childFolders = $childFolders;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getFiles(): ArrayCollection
    {
        return $this->files;
    }

    /**
     * @param ArrayCollection $files
     */
    public function setFiles(ArrayCollection $files): void
    {
        $this->files = $files;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags(): ArrayCollection
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection $tags
     */
    public function setTags(ArrayCollection $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'parent' => $this->parent->getId(),
            'name' => $this->name,
            'tags' => $this->tags,
        ];
    }
}
