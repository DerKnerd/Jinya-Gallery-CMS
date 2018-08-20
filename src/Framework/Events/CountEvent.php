<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:51
 */

namespace Jinya\Framework\Events;

use Symfony\Component\EventDispatcher\Event;

class CountEvent extends Event
{
    public static const ARTWORKS_PRE_COUNT = 'ArtworksPreCount';

    public static const ARTWORKS_POST_COUNT = 'ArtworksPostCount';

    /** @var string */
    private $keyword;

    /** @var int */
    private $count;

    /**
     * CountEvent constructor.
     * @param string $keyword
     * @param int $count
     */
    public function __construct(string $keyword, int $count)
    {
        $this->keyword = $keyword;
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
}
