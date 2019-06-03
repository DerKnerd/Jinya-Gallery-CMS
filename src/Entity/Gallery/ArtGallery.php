<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 07.11.2017
 * Time: 17:33
 */

namespace Jinya\Entity\Gallery;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Base\BaseArtEntity;
use Jinya\Entity\Base\HistoryEnabledEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="art_gallery")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("name", message="backend.galleries.name.not_unique")
 * @UniqueEntity("slug", message="backend.galleries.slug.not_unique")
 */
class ArtGallery extends HistoryEnabledEntity implements GalleryInterface
{
    use BaseArtEntity;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Artwork\ArtworkPosition", mappedBy="gallery", cascade={"persist"})
     */
    private $artworks;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Jinya\Entity\Label\Label", inversedBy="galleries", cascade={"persist"})
     */
    private $labels;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $background;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $orientation = self::HORIZONTAL;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $masonry = false;

    /**
     * Gallery constructor.
     */
    public function __construct()
    {
        $this->artworks = new ArrayCollection();
        $this->labels = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function isMasonry(): bool
    {
        return $this->masonry;
    }

    /**
     * @param bool $masonry
     */
    public function setMasonry(bool $masonry): void
    {
        $this->masonry = $masonry;
    }

    /**
     * @return Collection
     */
    public function getLabels(): ?Collection
    {
        return $this->labels;
    }

    /**
     * @param Collection $labels
     */
    public function setLabels(Collection $labels): void
    {
        $this->labels = $labels;
    }

    /**
     * @return string
     */
    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    /**
     * @param string $orientation
     */
    public function setOrientation(string $orientation)
    {
        $this->orientation = $orientation;
    }

    /**
     * @return Collection
     */
    public function getArtworks(): ?Collection
    {
        return $this->artworks;
    }

    /**
     * @param Collection $artworks
     */
    public function setArtworks(Collection $artworks)
    {
        $this->artworks = $artworks;
    }

    /**
     * @return string
     */
    public function getBackground(): ?string
    {
        return $this->background;
    }

    /**
     * @param string $background
     */
    public function setBackground(?string $background)
    {
        $this->background = $background;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'background' => $this->background,
            'creator' => $this->getCreator(),
            'createdAt' => $this->getCreatedAt(),
            'lastUpdatedAt' => $this->getLastUpdatedAt(),
            'updatedBy' => $this->getUpdatedBy(),
            'slug' => $this->slug,
        ];
    }
}
