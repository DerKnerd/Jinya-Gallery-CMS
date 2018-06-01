<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 01.06.18
 * Time: 08:06
 */

namespace Jinya\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\HistoryEnabledEntity;
use Jinya\Entity\SlugEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="youtube_video")
 */
class YoutubeVideo extends HistoryEnabledEntity
{
    use SlugEntity;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $url;

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * Specify data which should be serialized to JSON
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'id' => $this->id,
            'url' => $this->url,
            'createdAt' => $this->getCreatedAt(),
            'createdBy' => $this->getCreator(),
            'lastUpdatedAt' => $this->getLastUpdatedAt(),
            'updatedBy' => $this->getUpdatedBy(),
        ];
    }
}
