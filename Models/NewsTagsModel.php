<?php

namespace CMW\Model\News;

use CMW\Entity\News\NewsTagsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;


/**
 * Class @NewsTagsModel
 * @package News
 * @author Teyir
 * @version 1.0
 */
class NewsTagsModel extends AbstractModel
{

    public function createTag(string $name, ?string $icon, ?string $color): bool
    {
        $data = [
            'name' => $name,
            'icon' => $icon,
            'color' => $color,
        ];

        $sql = "INSERT INTO cmw_news_tags (news_tags_name, news_tags_icon, news_tags_color) 
                            VALUES (:name, :icon, :color)";

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute($data);
    }

    public function editTag(int $id, string $name, ?string $icon, ?string $color): bool
    {
        $data = [
            'id' => $id,
            'name' => $name,
            'icon' => $icon,
            'color' => $color,
        ];

        $sql = "UPDATE cmw_news_tags SET news_tags_name = :name, news_tags_icon = :icon, news_tags_color = :color 
                     WHERE news_tags_id = :id";
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute($data);
    }

    public function deleteTag(int $id): bool
    {
        $sql = "DELETE FROM cmw_news_tags WHERE news_tags_id = :id";
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['id' => $id]);
    }

    public function addTagToNews(int $tagId, int $newsId): bool
    {
        $sql = "INSERT INTO cmw_news_tags_list (news_id, news_tags_id) VALUES (:news_id, :tags_id)";
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['news_id' => $newsId, 'tags_id' => $tagId]);
    }

    public function clearTagsForANews(int $newsId): bool
    {
        $sql = "DELETE FROM cmw_news_tags_list WHERE news_id = :news_id";
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['news_id' => $newsId]);
    }

    /**
     * @return \CMW\Entity\News\NewsTagsEntity[]
     */
    public function getTags(): array
    {
        $sql = "SELECT * FROM cmw_news_tags";
        $db = DatabaseManager::getInstance();
        $req = $db->query($sql);

        if (!$req) {
            return [];
        }

        $toReturn = [];

        foreach ($req as $tag) {
            $toReturn[] = new NewsTagsEntity(
                $tag['news_tags_id'],
                $tag['news_tags_name'],
                $tag['news_tags_icon'],
                $tag['news_tags_color'],
            );
        }

        return $toReturn;
    }

    public function getTagById(int $tagId): ?NewsTagsEntity
    {
        $sql = "SELECT * FROM cmw_news_tags WHERE news_tags_id = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $tagId])) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        return new NewsTagsEntity(
            $res['news_tags_id'],
            $res['news_tags_name'],
            $res['news_tags_icon'],
            $res['news_tags_color'],
        );
    }

    public function getTagsForNewsById(int $newsId): array
    {
        $sql = "SELECT * FROM cmw_news_tags 
                JOIN cmw_news_tags_list list 
                    ON cmw_news_tags.news_tags_id = list.news_tags_id
                WHERE list.news_id = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $newsId])) {
            return [];
        }

        $res = $req->fetchAll();

        if (!$res) {
            return [];
        }

        $toReturn = [];

        foreach ($res as $tag) {
            $toReturn[] = new NewsTagsEntity(
                $tag['news_tags_id'],
                $tag['news_tags_name'],
                $tag['news_tags_icon'],
                $tag['news_tags_color'],
            );
        }

        return $toReturn;
    }

    /**
     * @param int $tagId
     * @return int
     * @desc Return the number of news for a specific tag.
     */
    public function getNewsNumberForTag(int $tagId): int
    {
        $sql = "SELECT COUNT('*') AS `count` FROM cmw_news_tags_list WHERE news_tags_id = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $tagId])) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['count'] ?? 0;
    }
}