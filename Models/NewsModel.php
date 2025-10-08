<?php

namespace CMW\Model\News;

use CMW\Entity\News\NewsBannedPlayersEntity;
use CMW\Entity\News\NewsEntity;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Editor\EditorManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\ExpectedValues;
use JsonException;
use ReflectionException;
use function count;
use function is_null;
use function unlink;

/**
 * Class @NewsModel
 * @package News
 * @author Teyir
 * @version 1.0
 */
class NewsModel extends AbstractModel
{
    /**
     * @param string $title
     * @param string $desc
     * @param int $comm
     * @param int $likes
     * @param string $content
     * @param string $slug
     * @param int $authorId
     * @param string $imageName
     * @param int $status
     * @param string|null $scheduledDate
     * @return NewsEntity|null
     */
    public function createNews(
        string  $title,
        string  $desc,
        int     $comm,
        int     $likes,
        string  $content,
        string  $slug,
        int     $authorId,
        string  $imageName,
        int     $status,
        ?string $scheduledDate = null,
    ): ?NewsEntity
    {
        $var = [
            'title' => $title,
            'desc' => $desc,
            'comm' => $comm,
            'likes' => $likes,
            'content' => $content,
            'slug' => $slug,
            'authorId' => $authorId,
            'imageName' => $imageName,
            'status' => $status,
            'scheduledDate' => $scheduledDate,
        ];

        $sql = 'INSERT INTO cmw_news (news_title, news_desc, news_comments_status, news_likes_status, news_content, 
                news_slug, news_author, news_image_name, news_status, news_date_scheduled) 
                VALUES (:title, :desc, :comm, :likes, :content, :slug, :authorId, :imageName, :status, :scheduledDate)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute($var)) {
            return null;
        }

        $id = $db->lastInsertId();
        return $this->getNewsById($id, true);
    }

    /**
     * @param int $newsId
     * @param bool $ignoreCache
     * @return NewsEntity|null
     */
    public function getNewsById(int $newsId, bool $ignoreCache = false): ?NewsEntity
    {
        if (!$ignoreCache) {
            $cachedData = SimpleCacheManager::getCache('news_id_' . $newsId, 'News');

            if (!is_null($cachedData)) {
                try {
                    return NewsEntity::toEntity($cachedData);
                } catch (ReflectionException) {
                }
            }
        }

        $sql = 'SELECT * FROM cmw_news WHERE news_id=:news_id';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(['news_id' => $newsId])) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $author = UsersModel::getInstance()->getUserById($res['news_author']);
        $newsLikes = NewsLikesModel::getInstance()->getLikesForNews($res['news_id']);

        $toReturn = new NewsEntity(
            $res['news_id'],
            $res['news_title'],
            $res['news_desc'],
            $res['news_comments_status'],
            $res['news_likes_status'],
            $res['news_status'],
            $res['news_date_scheduled'] ?? null,
            $res['news_content'],
            $res['news_content'],
            $res['news_slug'],
            $author,
            $res['news_views'],
            $res['news_image_name'],
            $res['news_date_created'],
            $res['news_date_updated'],
            $newsLikes,
            NewsCommentsModel::getInstance()->getCommentsForNews($res['news_id']),
            NewsTagsModel::getInstance()->getTagsForNewsById($res['news_id']),
        );

        SimpleCacheManager::storeCache($toReturn->toArray(), 'news_id_' . $newsId, 'News');

        return $toReturn;
    }

    /**
     * @param string $newsSlug
     * @param bool $ignoreCache
     * @return NewsEntity|null
     */
    public function getNewsBySlug(string $newsSlug, bool $ignoreCache = false): ?NewsEntity
    {
        if (!$ignoreCache) {
            $cachedData = SimpleCacheManager::getCache("news_slug_$newsSlug", 'News');

            if (!is_null($cachedData)) {
                try {
                    return NewsEntity::toEntity($cachedData);
                } catch (ReflectionException) {
                }
            }
        }

        $sql = 'SELECT * FROM cmw_news WHERE news_slug=:news_slug';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(['news_slug' => $newsSlug])) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $author = UsersModel::getInstance()->getUserById($res['news_author']);
        $newsLikes = NewsLikesModel::getInstance()->getLikesForNews($res['news_id']);
        $newsComments = NewsCommentsModel::getInstance()->getCommentsForNews($res['news_id']);

        $toReturn = new NewsEntity(
            $res['news_id'],
            $res['news_title'],
            $res['news_desc'],
            $res['news_comments_status'],
            $res['news_likes_status'],
            $res['news_status'],
            $res['news_date_scheduled'] ?? null,
            $res['news_content'],
            $res['news_content'],
            $res['news_slug'],
            $author,
            $res['news_views'],
            $res['news_image_name'],
            $res['news_date_created'],
            $res['news_date_updated'],
            $newsLikes,
            $newsComments,
            NewsTagsModel::getInstance()->getTagsForNewsById($res['news_id'])
        );

        SimpleCacheManager::storeCache($toReturn->toArray(), 'news_slug_' . $newsSlug, 'News');

        return $toReturn;
    }

    /**
     * @param bool $status (default: null), if True return only published news, if False return only unpublished news, if null return all news
     * @param bool $ignoreCache
     * @return array
     * @desc return all news
     */
    public function getNews(?bool $status = null, bool $ignoreCache = false): array
    {

        if (!$ignoreCache) {
            $cachedData = SimpleCacheManager::getCache('news', 'News');
            if (!is_null($cachedData)) {
                try {
                    return NewsEntity::fromJsonList($cachedData);
                } catch (ReflectionException|JsonException) {
                }
            }
        }

        $data = [];

        $sql = 'SELECT news_id FROM cmw_news';

        if (!is_null($status)) {
            $sql .= ' WHERE news_status = :status';
            $data['status'] = $status;
        }

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return [];
        }

        $toReturn = [];

        while ($news = $res->fetch()) {
            Utils::addIfNotNull($toReturn, $this->getNewsById($news['news_id'], $ignoreCache));
        }

        try {
            SimpleCacheManager::storeCache(NewsEntity::toJsonList($toReturn), 'news', 'News');
        } catch (JsonException) {
        }

        return $toReturn;
    }

    /**
     * @param bool $status (default: true), if True return only published news, if False return only unpublished news, if null return all news
     * @param int $limit
     * @param string $order
     * @return NewsEntity[]
     */
    public function getSomeNews(int $limit, #[ExpectedValues(values: ['DESC', 'ASC'])] string $order = 'DESC', ?bool $status = true): array
    {
        $cachedData = SimpleCacheManager::getCache("news_order_{$order}_{$limit}_$status", 'News');
        if (!is_null($cachedData)) {
            try {
                return NewsEntity::fromJsonList($cachedData);
            } catch (ReflectionException|JsonException) {
            }
        }

        $data = [
            'limit' => $limit,
        ];

        $sql = 'SELECT news_id FROM cmw_news';

        if (!is_null($status)) {
            $sql .= ' WHERE news_status = :status';
            $data['status'] = $status;
        }

        $sql .= $order === 'ASC'
            ? ' ORDER BY `cmw_news`.`news_id` LIMIT :limit'
            : ' ORDER BY `cmw_news`.`news_id` DESC LIMIT :limit';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute($data)) {
            return [];
        }

        $toReturn = [];

        while ($news = $res->fetch()) {
            $toReturn[] = $this->getNewsById($news['news_id']);
        }

        try {
            SimpleCacheManager::storeCache(NewsEntity::toJsonList($toReturn), "news_order_{$order}_{$limit}_$status", 'News');
        } catch (JsonException) {
        }

        return $toReturn;
    }

    /**
     * @param int $newsId
     * @param string $title
     * @param string $desc
     * @param int $comm
     * @param int $likes
     * @param string $content
     * @param string $slug
     * @param string|null $imageName
     * @param int $status
     * @param string|null $scheduledDate
     * @return NewsEntity|null
     */
    public function updateNews(
        int         $newsId,
        string      $title,
        string      $desc,
        int         $comm,
        int         $likes,
        string      $content,
        string      $slug,
        string|null $imageName,
        int         $status,
        ?string    $scheduledDate = null,
    ): ?NewsEntity
    {
        $var = [
            'newsId' => $newsId,
            'title' => $title,
            'desc' => $desc,
            'comm' => $comm,
            'likes' => $likes,
            'content' => $content,
            'slug' => $slug,
            'imageName' => $imageName,
            'status' => $status,
            'scheduledDate' => $scheduledDate,
        ];

        $sql = 'UPDATE cmw_news SET news_title = :title, news_desc = :desc, news_comments_status = :comm, 
                    news_likes_status = :likes, news_content = :content, news_slug = :slug, 
                    news_image_name = :imageName, news_status = :status, news_date_scheduled = :scheduledDate
                WHERE news_id = :newsId';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if ($req->execute($var)) {
            return $this->getNewsById($newsId, true);
        }

        return null;
    }

    /**
     * @param int $newsId
     * @return void
     */
    public function deleteNews(int $newsId): void
    {
        // Delete the image file
        unlink(EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/News/' . $this->getNewsById($newsId)?->getImageName());

        $newsContent = $this->getNewsById($newsId)?->getContent();
        EditorManager::getInstance()->deleteEditorImageInContent($newsContent);

        $sql = 'DELETE FROM cmw_news WHERE news_id=:news_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(['news_id' => $newsId]);
    }

    /**
     * @return NewsBannedPlayersEntity[]
     */
    public function getBannedUsers(): array
    {
        $sql = 'SELECT news_banned_players_player_id FROM cmw_news_banned_players';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($player = $res->fetch()) {
            Utils::addIfNotNull($toReturn, $this->getBannedUser($player['news_banned_players_player_id']));
        }

        return $toReturn;
    }

    /**
     * @param int $userId
     * @return NewsBannedPlayersEntity|null
     */
    public function getBannedUser(int $userId): ?NewsBannedPlayersEntity
    {
        $sql = 'SELECT news_banned_players_id, news_banned_players_player_id, news_banned_players_author_id, news_banned_players_date
                FROM cmw_news_banned_players WHERE news_banned_players_player_id = :userId';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(['userId' => $userId])) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $player = UsersModel::getInstance()->getUserById($userId);
        $author = UsersModel::getInstance()->getUserById($res['news_banned_players_author_id']);

        return new NewsBannedPlayersEntity(
            $res['news_banned_players_id'],
            $player,
            $author,
            $res['news_banned_players_date']
        );
    }

    /**
     * @param int $userId
     * @param int $authorId
     * @return NewsBannedPlayersEntity|null
     */
    public function banPlayer(int $userId, int $authorId): ?NewsBannedPlayersEntity
    {
        if (!$this->isUserBanned($userId)) {
            $var = [
                'userId' => $userId,
                'authorId' => $authorId,
            ];

            $sql = 'INSERT INTO cmw_news_banned_players (news_banned_players_player_id, news_banned_players_author_id) 
                    VALUES (:userId, :authorId)';

            $db = DatabaseManager::getInstance();

            $res = $db->prepare($sql);

            if ($res->execute($var)) {
                return $this->getBannedUser($userId);
            }
        }
        return null;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isUserBanned(int $userId): bool
    {
        $sql = 'SELECT news_banned_players_id FROM `cmw_news_banned_players` 
                              WHERE news_banned_players_player_id = :user_id';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute(['user_id' => $userId]);

        return count($res->fetchAll()) !== 0;
    }

    /**
     * @param int $newsId
     * @return void
     */
    public function incrementViews(int $newsId): void
    {
        $sql = 'UPDATE cmw_news SET news_views = news_views + 1 WHERE news_id = :id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute(['id' => $newsId]);
    }
}
