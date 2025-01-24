<?php

namespace CMW\Mapper\News\Front;

use CMW\Entity\News\FrontApiNewsEntity;
use CMW\Entity\News\NewsEntity;
use CMW\Entity\News\NewsTagsEntity;
use function array_map;

class NewsFrontMapper
{
    /**
     * @param NewsEntity[] $data
     * @return FrontApiNewsEntity[]
     */
    public static function mapToFront(array $data): array
    {
        return array_map(static fn($article) => self::mapArticle($article), $data);
    }

    public static function mapArticle(NewsEntity $data): FrontApiNewsEntity
    {
        return new FrontApiNewsEntity(
            $data->getNewsId(),
            $data->getTitle(),
            $data->getDescription(),
            $data->getAuthor()?->getPseudo() ?? 'Unknown',
            $data->getAuthor()?->getUserPicture()?->getImage() ?? '',
            self::mapTags($data->getTags()),
            $data->getDateCreatedFormatted(),
            $data->getFullUrl(),
            $data->getFullImageLink(),
        );
    }

    /**
     * @param NewsTagsEntity[] $data
     * @return string[]
     */
    public static function mapTags(array $data): array
    {
        return array_map(static fn($tag) => $tag->getName(), $data);
    }
}