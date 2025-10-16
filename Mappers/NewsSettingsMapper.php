<?php

namespace CMW\Mapper\News;

use CMW\Entity\News\NewsSettingsEntity;

class NewsSettingsMapper
{
    public static function map(array $data): ?NewsSettingsEntity
    {
        if (empty($data)) {
            return null;
        }

        if (!isset($data['enable_scheduled_publishing'], $data['cron_key'])) {
            return null;
        }

        return new NewsSettingsEntity(
            (bool)$data['enable_scheduled_publishing'],
            $data['cron_key'],
            $data['slug_prefix'] ?? 'news'
        );
    }
}