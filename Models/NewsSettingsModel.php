<?php

namespace CMW\Model\News;

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


    public function getSettings(): ?NewsSettingsEntity
    {
        $data = [
            'enable_scheduled_publishing' => CoreModel::getInstance()->fetchOption(self::KEY_ENABLE_SCHEDULED_PUBLISHING),
            'cron_key' => CoreModel::getInstance()->fetchOption(self::KEY_CRON_TOKEN),
        ];

        return NewsSettingsMapper::map($data);
    }

    public function setSettings(?NewsSettingsEntity $settings): bool
    {
        if ($settings === null) {
            return false;
        }

        return CoreModel::getInstance()->updateOption(self::KEY_ENABLE_SCHEDULED_PUBLISHING, $settings->isEnableScheduledPublishing() ? '1' : '0');
    }

    public function setCronToken(string $token): bool
    {
        return CoreModel::getInstance()->updateOption(self::KEY_CRON_TOKEN, $token);
    }
}
