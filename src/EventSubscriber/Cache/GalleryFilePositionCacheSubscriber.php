<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 18:59
 */

namespace Jinya\EventSubscriber\Cache;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Media\Gallery;
use Jinya\Framework\Events\Media\GalleryFilePositionDeleteEvent;
use Jinya\Framework\Events\Media\GalleryFilePositionEvent;
use Jinya\Framework\Events\Media\GalleryFilePositionUpdateEvent;
use Jinya\Framework\Events\Media\GalleryFilePositionUpdateFileEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GalleryFilePositionCacheSubscriber implements EventSubscriberInterface
{
    /** @var CacheBuilderInterface */
    private CacheBuilderInterface $cacheBuilder;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /**
     * GalleryFileCacheSubscriber constructor.
     */
    public function __construct(CacheBuilderInterface $cacheBuilder, EntityManagerInterface $entityManager)
    {
        $this->cacheBuilder = $cacheBuilder;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            GalleryFilePositionEvent::POST_SAVE => 'onGalleryFilePositionSave',
            GalleryFilePositionDeleteEvent::POST_DELETE => 'onGalleryFilePositionDelete',
            GalleryFilePositionUpdateEvent::POST_UPDATE => 'onGalleryFilePositionUpdate',
            GalleryFilePositionUpdateFileEvent::POST_UPDATE_FILE => 'onGalleryFilePositionUpdateFile',
        ];
    }

    public function onGalleryFilePositionDelete(GalleryFilePositionDeleteEvent $event): void
    {
        $this->cacheBuilder->buildCacheBySlugAndType(
            $event->getGalleryFilePosition()->getGallery()->getSlug(),
            CacheBuilderInterface::MEDIA_GALLERY
        );
    }

    public function onGalleryFilePositionSave(GalleryFilePositionEvent $event): void
    {
        $GalleryFilePosition = $event->getGalleryFilePosition();
        /* @noinspection NullPointerExceptionInspection */
        $this->cacheBuilder->buildCacheBySlugAndType(
            $GalleryFilePosition->getGallery()->getSlug(),
            CacheBuilderInterface::MEDIA_GALLERY
        );
    }

    public function onGalleryFilePositionUpdate(GalleryFilePositionUpdateEvent $event): void
    {
        $this->cacheBuilder->buildCacheBySlugAndType($event->getGallerySlug(), CacheBuilderInterface::MEDIA_GALLERY);
    }

    public function onGalleryFilePositionUpdateFile(GalleryFilePositionUpdateFileEvent $event): void
    {
        $galleries = $this->entityManager->createQueryBuilder()
            ->select('gallery.slug')
            ->from(Gallery::class, 'gallery')
            ->join('gallery.files', 'position')
            ->where('position.id = :id')
            ->setParameter('id', $event->getPositionId())
            ->getQuery()
            ->getScalarResult();

        foreach ($galleries as $gallery) {
            $this->cacheBuilder->buildCacheBySlugAndType($gallery['slug'], CacheBuilderInterface::MEDIA_GALLERY);
        }
    }
}
