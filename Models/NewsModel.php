<?php

namespace CMW\Model\News;

use CMW\Entity\News\NewsBannedPlayersEntity;
use CMW\Entity\News\NewsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Model\Users\UsersModel;
use JetBrains\PhpStorm\ExpectedValues;


/**
 * Class @NewsModel
 * @package News
 * @author Teyir
 * @version 1.0
 */
class NewsModel extends AbstractModel
{
    public function createNews(string $title, string $desc, int $comm, int $likes, string $content, string $slug, int $authorId, string $imageName): ?NewsEntity
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
        ];

        $sql = "INSERT INTO cmw_news (news_title, news_desc, news_comments_status, news_likes_status, news_content, 
                news_slug, news_author, news_image_name) 
                VALUES (:title, :desc, :comm, :likes, :content, :slug, :authorId, :imageName)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);


        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            return $this->getNewsById($id);
        }

        return null;
    }

    public function getNewsById(int $newsId): ?NewsEntity
    {

        $sql = "SELECT * FROM cmw_news WHERE news_id=:news_id";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);


        if (!$res->execute(["news_id" => $newsId])) {
            return null;
        }

        $res = $res->fetch();

        if (empty($res)) {
            return null;
        }

        $author = UsersModel::getInstance()->getUserById($res["news_author"]);
        $newsLikes = NewsLikesModel::getInstance()->getLikesForNews($res['news_id']);

        return new NewsEntity(
            $res['news_id'],
            $res['news_title'],
            $res['news_desc'],
            $res['news_comments_status'],
            $res['news_likes_status'],
            $res["news_content"],
            $res["news_content"],
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
    }

    public function getNewsBySlug(string $newsSlug): ?NewsEntity
    {

        $sql = "SELECT * FROM cmw_news WHERE news_slug=:news_slug";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);


        if (!$res->execute(["news_slug" => $newsSlug])) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $author = UsersModel::getInstance()->getUserById($res["news_author"]);
        $newsLikes = NewsLikesModel::getInstance()->getLikesForNews($res['news_id']);
        $newsComments = NewsCommentsModel::getInstance()->getCommentsForNews($res['news_id']);

        return new NewsEntity(
            $res['news_id'],
            $res['news_title'],
            $res['news_desc'],
            $res['news_comments_status'],
            $res['news_likes_status'],
            $res["news_content"],
            $res["news_content"],
            $res['news_slug'],
            $author,
            $res['news_views'],
            $res['news_image_name'],
            $res['news_date_created'],
            $res['news_date_updated'],
            $newsLikes,
            $newsComments,
            NewsTagsModel::getInstance()->getTagsForNewsById($res['news_id']),
        );
    }

    /**
     * @return array
     * @desc return all news
     */
    public function getNews(): array
    {
        $sql = "SELECT news_id FROM cmw_news";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($news = $res->fetch()) {
            $toReturn[] = $this->getNewsById($news["news_id"]);
        }

        return $toReturn;
    }

    /**
     * @param int $limit
     * @param string $order
     * @return NewsEntity[]
     */
    public function getSomeNews(int $limit, #[ExpectedValues (values: ['DESC', 'ASC'])] string $order = "DESC"): array
    {

        $order === "ASC" ? $sql = "SELECT news_id FROM cmw_news ORDER BY `cmw_news`.`news_id` LIMIT :limit"
            : $sql = "SELECT news_id FROM cmw_news ORDER BY `cmw_news`.`news_id` DESC LIMIT :limit";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(["limit" => $limit])) {
            return [];
        }

        $toReturn = [];

        while ($news = $res->fetch()) {
            $toReturn[] = $this->getNewsById($news["news_id"]);
        }

        return $toReturn;
    }

    public function updateNews(int $newsId, string $title, string $desc, int $comm, int $likes, string $content, string $slug, string|null $imageName): ?NewsEntity
    {
        $var = [
            'newsId' => $newsId,
            'title' => $title,
            'desc' => $desc,
            'comm' => $comm,
            'likes' => $likes,
            'content' => $content,
            'slug' => $slug,
        ];

        $sql = "UPDATE cmw_news SET news_title = :title, news_desc = :desc, news_comments_status = :comm, 
                    news_likes_status = :likes, news_content = :content, news_slug = :slug WHERE news_id = :newsId";

        //Detect if we update the image
        if (!empty($image['name'])) {
            //Delete the old image
            ImagesManager::deleteImage($this->getNewsById($newsId)?->getImageName(), "News/");

            //Add the image to the var
            $var += ["imageName" => $imageName];

            //Update SQL
            $sql = "UPDATE cmw_news SET news_title = :title, news_desc = :desc, news_comments_status = :comm, 
                    news_likes_status = :likes, news_content = :content, news_slug = :slug, news_image_name = :imageName WHERE news_id = :newsId";
        }


        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if ($req->execute($var)) {
            return $this->getNewsById($newsId);
        }

        return null;
    }

    public function deleteNews(int $newsId): void
    {
        //Delete the image file
        unlink(EnvManager::getInstance()->getValue("DIR") . "Public/Uploads/News/" . $this->getNewsById($newsId)?->getImageName());

        $sql = "DELETE FROM cmw_news WHERE news_id=:news_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(["news_id" => $newsId]);
    }

    public function getBannedUsers(): array
    {

        $sql = "SELECT news_banned_players_player_id FROM cmw_news_banned_players";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($player = $res->fetch()) {
            $toReturn[] = $this->getBannedUser($player["news_banned_players_player_id"]);
        }

        return $toReturn;
    }

    public function getBannedUser(int $userId): ?NewsBannedPlayersEntity
    {

        $sql = "SELECT news_banned_players_id, news_banned_players_player_id, news_banned_players_author_id, news_banned_players_date
                FROM cmw_news_banned_players WHERE news_banned_players_player_id = :userId";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);


        if (!$res->execute(["userId" => $userId])) {
            return null;
        }

        $res = $res->fetch();

        $player = UsersModel::getInstance()->getUserById($userId);
        $author = UsersModel::getInstance()->getUserById($res['news_banned_players_author_id']);

        return new NewsBannedPlayersEntity(
            $res['news_banned_players_id'],
            $player,
            $author,
            $res['news_banned_players_date']
        );
    }

    public function banPlayer(int $userId): ?NewsBannedPlayersEntity
    {
        if (!$this->isUserBanned($userId)) {
            $var = [
                "userId" => $userId,
                "authorId" => UsersModel::getCurrentUser()?->getId(),
            ];

            $sql = "INSERT INTO cmw_news_banned_players (news_banned_players_player_id, news_banned_players_author_id) 
                    VALUES (:userId, :authorId)";

            $db = DatabaseManager::getInstance();

            $res = $db->prepare($sql);

            if ($res->execute($var)) {
                return $this->getBannedUser($userId);
            }
        }
        return null;
    }

    public function isUserBanned(int $userId): bool
    {
        $sql = "SELECT news_banned_players_id FROM `cmw_news_banned_players` 
                              WHERE news_banned_players_player_id = :user_id";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        $res->execute(["user_id" => $userId]);

        return count($res->fetchAll()) !== 0;
    }

    public function incrementViews(int $newsId): void
    {
        $sql = "UPDATE cmw_news SET news_views = news_views + 1 WHERE news_id = :id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute(["id" => $newsId]);
    }
}
