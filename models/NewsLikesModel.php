<?php

namespace CMW\Model\News;

use CMW\Entity\News\NewsLikesEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Model\Users\UsersModel;


/**
 * Class @NewsLikesModel
 * @package news
 * @author Teyir
 * @version 1.0
 */
class NewsLikesModel extends DatabaseManager
{

    /**
     * @return int
     * @desc Get all the likes for all the news
     */
    public function getTotalLikes(): int
    {
        $sql = "SELECT news_like_news_id FROM cmw_news_likes";

        $db = self::getInstance();
        $req = $db->prepare($sql);
        $res = $req->execute();

        if ($res) {
            $lines = $req->fetchAll();

            return count($lines);
        }

        return 0;
    }

    public function userCanLike(int $newsId, int $userId): bool
    {
        $sql = "SELECT news_like_id FROM `cmw_news_likes` WHERE news_like_news_id = :news_id AND news_like_user_id = :user_id";

        $db = self::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array("news_id" => $newsId, "user_id" => $userId));

        return count($res->fetchAll()) === 0;
    }

    public function storeLike(int $newsId, int $userId): ?NewsLikesEntity
    {
        $sql = "INSERT INTO cmw_news_likes (news_like_news_id, news_like_user_id) VALUES (:news_id, :user_id)";

        $db = self::getInstance();
        $res = $db->prepare($sql);


        if ($res->execute(array("news_id" => $newsId, "user_id" => $userId))) {
            $id = $db->lastInsertId();
            return $this->getLikesForNews($id);
        }

        return null;
    }

    /**
     * @param int $newsId
     * @return \CMW\Entity\News\NewsLikesEntity|null
     * @desc Get the likesEntity
     */
    public function getLikesForNews(int $newsId): ?NewsLikesEntity
    {
        $sql = "SELECT * FROM cmw_news_likes WHERE news_like_news_id = :news_id";

        $db = self::getInstance();
        $res = $db->prepare($sql);


        if (!$res->execute(array("news_id" => $newsId))) {
            return null;
        }


        $res = $res->fetch();


        $totalLikes = $this->getTotalLikesForNews($newsId);

        if ($res) {
            $user = (new UsersModel())->getUserById($res["news_like_user_id"]);
        }

        return new NewsLikesEntity(
            $res['news_like_id'] ?? null,
            $user ?? null,
            $res['news_like_date'] ?? null,
            $totalLikes,
            $newsId
        );
    }

    /**
     * @param int $newsId
     * @return int
     * @desc Get all the likes for a specific news
     */
    public function getTotalLikesForNews(int $newsId): int
    {
        $sql = "SELECT news_like_news_id FROM cmw_news_likes WHERE news_like_news_id = :news_id";

        $db = self::getInstance();
        $req = $db->prepare($sql);
        $res = $req->execute(array("news_id" => $newsId));

        if ($res) {
            $lines = $req->fetchAll();

            return count($lines);
        }

        return 0;
    }

}