<?php

namespace CMW\Entity\News\Dto;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractEntity;

/**
 * Class: @NewsScheduledToPublishEntity
 * @package News
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/entities
 */
class NewsScheduledToPublishEntity extends AbstractEntity
{
    private int $newsId;
    private string $newsSlug;

    /**
     * @param int $newsId
     * @param string $newsSlug
     */
    public function __construct(int $newsId, string $newsSlug)
    {
        $this->newsId = $newsId;
        $this->newsSlug = $newsSlug;
    }

    /**
     * @return int
     */
    public function getNewsId(): int
    {
        return $this->newsId;
    }

    /**
     * @return string
     */
    public function getNewsSlug(): string
    {
        return $this->newsSlug;
    }


    /**
     * @return string
     */
    public function getFullUrl(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'news/' . $this->newsSlug;
    }
}
