<?php

namespace CMW\Model\News;

use CMW\Controller\News\Admin\NewsSettingsAdminController;
use CMW\Entity\News\NewsSettingsEntity;
use CMW\Manager\Package\AbstractModel;
use CMW\Mapper\News\NewsSettingsMapper;
use CMW\Model\Core\CoreModel;

/**
 * Class: @NewsSettingsModel
 * @package News
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/models
 */
class NewsSettingsModel extends AbstractModel
{
    const string KEY_ENABLE_SCHEDULED_PUBLISHING = 'news_enable_scheduled_publishing';
    const string KEY_CRON_TOKEN = 'news_cron_token';
    const string KEY_SLUG_PREFIX = 'news_slug_prefix';


    public function getSettings(): ?NewsSettingsEntity
    {
        $data = [
            'enable_scheduled_publishing' => CoreModel::getInstance()->fetchOption(self::KEY_ENABLE_SCHEDULED_PUBLISHING),
            'cron_key' => CoreModel::getInstance()->fetchOption(self::KEY_CRON_TOKEN),
            'slug_prefix' => CoreModel::getInstance()->fetchOption(self::KEY_SLUG_PREFIX),
        ];

        return NewsSettingsMapper::map($data);
    }

    public function setSettings(?NewsSettingsEntity $settings): bool
    {
        if ($settings === null) {
            return false;
        }

        $publishingUpdate = CoreModel::getInstance()->updateOption(self::KEY_ENABLE_SCHEDULED_PUBLISHING, $settings->isEnableScheduledPublishing() ? '1' : '0');
        $slugUpdate = CoreModel::getInstance()->updateOption(self::KEY_SLUG_PREFIX, $settings->getSlugPrefix());

        return $publishingUpdate && $slugUpdate;
    }

    public function setCronToken(string $token): bool
    {
        return CoreModel::getInstance()->updateOption(self::KEY_CRON_TOKEN, $token);
    }

    /**
     * Get the news slug prefix from settings (with fallback to 'news')
     * @return string
     */
    public static function getNewsSlugPrefix(): string
    {
        $slugPrefix = CoreModel::getInstance()->fetchOption(self::KEY_SLUG_PREFIX);
        return $slugPrefix ?: NewsSettingsAdminController::getInstance()->defaultPrefixSlug;
    }
}
