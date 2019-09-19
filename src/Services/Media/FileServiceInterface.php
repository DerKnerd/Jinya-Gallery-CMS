<?php

namespace Jinya\Services\Media;

use Jinya\Entity\Media\File;

interface FileServiceInterface
{
    /**
     * Gets a list of all files in the folder and filtered by the given keyword and tag
     *
     * @param string $keyword
     * @param string $tag
     * @param string $type
     * @return File[]
     */
    public function getAll(string $keyword = '', string $tag = '', string $type = ''): array;

    /**
     * Counts all files filtered by the given keyword in the given folder and tag
     *
     * @param string $keyword
     * @param string $tag
     * @param string $type
     * @return int
     */
    public function countAll(string $keyword = '', string $tag = '', string $type = ''): int;

    /**
     * Saves or update the given file
     *
     * @param File $file
     * @return File
     */
    public function saveOrUpdate(File $file): File;

    /**
     * Deletes the given file
     *
     * @param File $file
     */
    public function delete(File $file): void;

    /**
     * Gets the file by slug or id
     *
     * @param int $id
     * @return File
     */
    public function get(int $id): File;
}