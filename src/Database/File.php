<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Utils\LoadableEntity;
use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiHiddenField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use DateTime;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

#[OpenApiModel("Files are the primary content of Jinya, files can have any type")]
class File extends LoadableEntity
{
    #[OpenApiField(required: true, array: true, structure: OpenApiField::CHANGED_BY_STRUCTURE, name: 'created')]
    public int $creatorId;
    #[OpenApiField(required: true, array: true, structure: OpenApiField::CHANGED_BY_STRUCTURE, name: 'updated')]
    public int $updatedById;
    #[OpenApiHiddenField]
    public DateTime $createdAt;
    #[OpenApiHiddenField]
    public DateTime $lastUpdatedAt;
    #[OpenApiField(required: false, defaultValue: '')]
    public string $path = '';
    #[OpenApiField(required: true)]
    public string $name = '';
    #[OpenApiField(required: true)]
    public string $type = '';

    /**
     * @inheritDoc
     * @return File
     */
    public static function findById(int $id): ?object
    {
        return self::fetchSingleById(
            'file',
            $id,
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, creator_id, updated_by_id, created_at, last_updated_at, path, name, type FROM file WHERE name LIKE :keyword';
        $result = self::executeStatement($sql, ['keyword' => "%$keyword%"]);

        return self::hydrateMultipleResults(
            $result,
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray(
            'file',
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Gets the uploading chunks
     *
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getUploadChunks(): Iterator
    {
        return UploadingFileChunk::findByFile($this->id);
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;

        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser->id;

        $this->id = $this->internalCreate(
            'file',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('file');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;
        $this->internalUpdate(
            'file',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    #[ArrayShape([
        'id' => "int",
        'name' => "string",
        'type' => "string",
        'path' => "string",
        'created' => "array",
        'updated' => "array"
    ])] public function format(): array
    {
        $creator = $this->getCreator();
        $updatedBy = $this->getUpdatedBy();

        return [
            'id' => $this->getIdAsInt(),
            'name' => $this->name,
            'type' => $this->type,
            'path' => $this->path,
            'created' => [
                'by' => [
                    'artistName' => $creator->artistName,
                    'email' => $creator->email,
                    'profilePicture' => $creator->profilePicture,
                ],
                'at' => $this->createdAt->format(DATE_ATOM),
            ],
            'updated' => [
                'by' => [
                    'artistName' => $updatedBy->artistName,
                    'email' => $updatedBy->email,
                    'profilePicture' => $updatedBy->profilePicture,
                ],
                'at' => $this->lastUpdatedAt->format(DATE_ATOM),
            ],
        ];
    }

    /**
     * Gets the creator of this file
     *
     * @return Artist
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getCreator(): Artist
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * Gets the artist that last updated this file
     *
     * @return Artist
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getUpdatedBy(): Artist
    {
        return Artist::findById($this->updatedById);
    }
}