<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:05
 */

namespace Jinya\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="artwork_position")
 * @UniqueEntity(fields={"gallery", "position"})
 */
class ArtworkPosition implements JsonSerializable
{
    use BaseEntity;

    /**
     * @var Gallery
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Gallery", inversedBy="artworks")
     */
    private $gallery;

    /**
     * @var Artwork
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Artwork", inversedBy="positions", cascade={"persist"})
     */
    private $artwork;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'artwork' => $this->getArtwork(),
            'gallery' => $this->getGallery(),
            'position' => $this->getPosition(),
        ];
    }

    /**
     * @return Artwork
     */
    public function getArtwork(): Artwork
    {
        return $this->artwork;
    }

    /**
     * @param Artwork $artwork
     */
    public function setArtwork(Artwork $artwork): void
    {
        $this->artwork = $artwork;
    }

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
