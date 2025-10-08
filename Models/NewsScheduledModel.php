<?php

namespace CMW\Model\News;

use CMW\Entity\News\Dto\NewsScheduledToPublishEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use ReflectionException;

/**
 * Class: @NewsScheduledModel
 * @package News
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/models
 */
class NewsScheduledModel extends AbstractModel
{
    /**
     * @return NewsScheduledToPublishEntity[]
     */
    public function getNewsToPublished(string $timestamp): array
    {
        $sql = "SELECT news_id, news_slug FROM cmw_news WHERE news_date_scheduled <= :timestamp AND news_date_scheduled";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['timestamp' => $timestamp])) {
            return [];
        }

        $res = $req->fetchAll();

        try {
            return NewsScheduledToPublishEntity::toEntityList($res);
        } catch (ReflectionException $e) {
            return [];
        }
    }

    /**
     * @param int[] $newsIds
     * @return bool
     */
    public function updateNewsStatus(array $newsIds): bool
    {
        $sql = "UPDATE cmw_news SET news_date_scheduled = NULL, news_status = 1 WHERE news_id IN (" . implode(',', array_map('\intval', $newsIds)) . ")";
        return DatabaseManager::getInstance()->query($sql) !== false;
    }
}
