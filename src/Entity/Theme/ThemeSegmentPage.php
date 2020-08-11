<?php

namespace Jinya\Entity\Theme;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\SegmentPage\SegmentPage;

/**
 * @ORM\Entity
 * @ORM\Table(name="theme_segment_page")
 */
class ThemeSegmentPage
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Theme\Theme")
     * @ORM\JoinColumn(nullable=false, name="theme_id", referencedColumnName="id")
     */
    private Theme $theme;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\SegmentPage\SegmentPage")
     * @ORM\JoinColumn(nullable=false, name="segment_page_id", referencedColumnName="id")
     */
    private SegmentPage $segmentPage;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     */
    private string $name;

    public function getTheme(): Theme
    {
        return $this->theme;
    }

    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }

    public function getSegmentPage(): SegmentPage
    {
        return $this->segmentPage;
    }

    public function setSegmentPage(SegmentPage $segmentPage): void
    {
        $this->segmentPage = $segmentPage;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
