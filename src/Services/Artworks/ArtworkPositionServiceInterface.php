<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.11.2017
 * Time: 18:08
 */

namespace Jinya\Services\Artworks;


use Jinya\Entity\ArtworkPosition;
use Jinya\Entity\Gallery;

interface ArtworkPositionServiceInterface
{
    /**
     * Saves the artwork in the given gallery at the given position
     *
     * @param string $gallerySlug
     * @param string $artworkSlug
     * @param int $position
     * @return bool
     */
    public function savePosition(string $gallerySlug, string $artworkSlug, int $position): bool;

    /**
     * Sets the artworks position to the new position
     *
     * @param string $gallerySlug
     * @param int $artworkPositionId
     * @param int $newPosition
     * @return void
     */
    public function updatePosition(string $gallerySlug, int $artworkPositionId, int $newPosition);

    /**
     * Deletes the given artwork position
     *
     * @param int $id
     * @return void
     */
    public function deletePosition(int $id);

    /**
     * Gets the artwork position for the given id
     *
     * @param int $id
     * @return ArtworkPosition
     */
    public function getPosition(int $id): ArtworkPosition;

    /**
     * Sets the artwork of the given artwork position to the new slug
     *
     * @param int $id
     * @param string $artworkSlug
     * @return void
     */
    public function updateArtwork(int $id, string $artworkSlug);

    /**
     * Gets all artworks for the given @see Gallery slug
     *
     * @param string $slug
     * @return array
     */
    public function getArtworks(string $slug): array;
}