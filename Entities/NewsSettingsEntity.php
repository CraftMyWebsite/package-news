<?php

namespace CMW\Entity\News;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Website;

/**
 * Class: @NewsSettingsEntity
 * @package News
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/entities
 */
class NewsSettingsEntity extends AbstractEntity
{
    private bool $enableScheduledPublishing;
    private string $cronKey;
    private string $slugPrefix;

    /**
     * @param bool $enableScheduledPublishing
     * @param string $cronKey
     * @param string $slugPrefix
     */
    public function __construct(bool $enableScheduledPublishing, string $cronKey, string $slugPrefix = 'news')
    {
        $this->enableScheduledPublishing = $enableScheduledPublishing;
        $this->cronKey = $cronKey;
        $this->slugPrefix = $slugPrefix;
    }

    public function isEnableScheduledPublishing(): bool
    {
        return $this->enableScheduledPublishing;
    }

    public function getCronKey(): string
    {
        return $this->cronKey;
    }

    public function getFullCronUrl(): string
    {
        return Website::getUrl() . "news/cron/scheduled/publishing?key=" . $this->getCronKey();
    }

    public function getSlugPrefix(): string
    {
        return $this->slugPrefix;
    }
}