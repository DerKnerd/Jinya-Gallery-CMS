<?php

namespace Jinya\Entity\SegmentPage;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Artwork\Artwork;
use Jinya\Entity\Base\BaseEntity;
use Jinya\Entity\Form\Form;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Entity\Video\Video;
use Jinya\Entity\Video\YoutubeVideo;

/**
 * @ORM\Entity
 * @ORM\Table(name="segment")
 */
class Segment
{
    public const ACTION_SCRIPT = 'script';
    public const ACTION_LINK = 'link';
    public const ACTION_NONE = 'none';

    use BaseEntity;

    /**
     * @var SegmentPage
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\SegmentPage\SegmentPage", inversedBy="segments", cascade={"persist"})
     */
    private $page;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var Artwork|null
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Artwork\Artwork")
     */
    private $artwork;

    /**
     * @var Form|null
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Form\Form")
     */
    private $form;

    /**
     * @var Video|null
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Video\Video")
     */
    private $video;

    /**
     * @var YoutubeVideo|null
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Video\YoutubeVideo")
     */
    private $youtubeVideo;

    /**
     * @var ArtGallery|null
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Gallery\ArtGallery")
     */
    private $artGallery;

    /**
     * @var VideoGallery|null
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Gallery\ArtGallery")
     */
    private $videoGallery;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $html;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $action;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $script;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $target;

    /**
     * @return SegmentPage
     */
    public function getPage(): SegmentPage
    {
        return $this->page;
    }

    /**
     * @param SegmentPage $page
     */
    public function setPage(SegmentPage $page): void
    {
        $this->page = $page;
    }

    private function reset(): void
    {
        $this->page = null;
        $this->form = null;
        $this->artwork = null;
        $this->artGallery = null;
        $this->html = null;
        $this->videoGallery = null;
        $this->video = null;
        $this->youtubeVideo = null;
    }

    /**
     * @return Artwork|null
     */
    public function getArtwork(): ?Artwork
    {
        return $this->artwork;
    }

    /**
     * @param Artwork|null $artwork
     */
    public function setArtwork(?Artwork $artwork): void
    {
        $this->reset();
        $this->artwork = $artwork;
    }

    /**
     * @return Form|null
     */
    public function getForm(): ?Form
    {
        return $this->form;
    }

    /**
     * @param Form|null $form
     */
    public function setForm(?Form $form): void
    {
        $this->reset();
        $this->form = $form;
    }

    /**
     * @return Video|null
     */
    public function getVideo(): ?Video
    {
        return $this->video;
    }

    /**
     * @param Video|null $video
     */
    public function setVideo(?Video $video): void
    {
        $this->reset();
        $this->video = $video;
    }

    /**
     * @return YoutubeVideo|null
     */
    public function getYoutubeVideo(): ?YoutubeVideo
    {
        return $this->youtubeVideo;
    }

    /**
     * @param YoutubeVideo|null $youtubeVideo
     */
    public function setYoutubeVideo(?YoutubeVideo $youtubeVideo): void
    {
        $this->reset();
        $this->youtubeVideo = $youtubeVideo;
    }

    /**
     * @return ArtGallery|null
     */
    public function getArtGallery(): ?ArtGallery
    {
        return $this->artGallery;
    }

    /**
     * @param ArtGallery|null $artGallery
     */
    public function setArtGallery(?ArtGallery $artGallery): void
    {
        $this->reset();
        $this->artGallery = $artGallery;
    }

    /**
     * @return VideoGallery|null
     */
    public function getVideoGallery(): ?VideoGallery
    {
        return $this->videoGallery;
    }

    /**
     * @param VideoGallery|null $videoGallery
     */
    public function setVideoGallery(?VideoGallery $videoGallery): void
    {
        $this->reset();
        $this->videoGallery = $videoGallery;
    }

    /**
     * @return string|null
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }

    /**
     * @param string|null $html
     */
    public function setHtml(?string $html): void
    {
        $this->reset();
        $this->html = $html;
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param string|null $action
     */
    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string|null
     */
    public function getScript(): ?string
    {
        return $this->script;
    }

    /**
     * @param string|null $script
     */
    public function setScript(?string $script): void
    {
        $this->script = $script;
    }

    /**
     * @return string|null
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param string|null $target
     */
    public function setTarget(?string $target): void
    {
        $this->target = $target;
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
