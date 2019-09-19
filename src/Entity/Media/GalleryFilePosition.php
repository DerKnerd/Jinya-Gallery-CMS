<?php

namespace Jinya\Entity\Media;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="gallery_file_position", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="gallery_and_position", columns={"gallery_id", "position"})
 * })
 */
class GalleryFilePosition
{
    use BaseEntity;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var Gallery
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\Gallery", inversedBy="files")
     */
    private $gallery;

    /**
     * @var File
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\File", inversedBy="galleries")
     */
    private $file;

    /**
     * @return Gallery
     */
    public function getGallery(): Gallery
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery
     */
    public function setGallery(Gallery $gallery): void
    {
        $this->gallery = $gallery;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}