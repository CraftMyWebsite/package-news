<?php

namespace CMW\Model\News;

use CMW\Entity\News\NewsCommentsLikesEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Model\Users\UsersModel;


/**
 * Class @NewsCommentsLikesModel
 * @package news
 * @author Teyir
 * @version 1.0
 */
class NewsCommentsLikesModel extends DatabaseManager
{

    /**
     * @return int
     * @desc Get all the likes for all the comments
     */
    public function getTotalLikes(): int
    {
        $sql = "SELECT news_comments_likes_comments_id FROM cmw_news_comments_likes";

        $db = self::getInstance();
        $req = $db->prepare($sql);
        $res = $req->execute();

        if ($res) {
            $lines = $req->fetchAll();

            return count($lines);
        }

        return 0;
    }

    /**
     * @param int $commentsId
     * @param ?int $userId
     * @return bool
     */
    public function userCanLike(int $commentsId, ?int $userId): bool
    {
        if ($userId === null){
            return  false;
        }

        $sql = "SELECT news_comments_likes_comments_id FROM `cmw_news_comments_likes`
                                       WHERE news_comments_likes_comments_id = :comments_id
                                         AND news_comments_likes_user_id = :user_id";

        $db = self::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array("comments_id" => $commentsId, "user_id" => $userId));

        return count($res->fetchAll()) === 0;
    }

    /**
     * @param int $commentsId
     * @param int $userId
     * @return \CMW\Entity\News\NewsCommentsLikesEntity|null
     */
    public function storeLike(int $commentsId, int $userId): ?NewsCommentsLikesEntity
    {
        $sql = "INSERT INTO cmw_news_comments_likes (news_comments_likes_comments_id, news_comments_likes_user_id) 
                        VALUES (:comments_id, :user_id)";

        $db = self::getInstance();
        $res = $db->prepare($sql);


        if ($res->execute(array("comments_id" => $commentsId, "user_id" => $userId))) {
            $id = $db->lastInsertId();
            return $this->getLikesById($id);
        }

        return null;
    }

    public function getLikesById(int $likeId): ?NewsCommentsLikesEntity
    {

        $sql = "SELECT news_comments_likes_id, 	news_comments_likes_comments_id , news_comments_likes_user_id,
                news_comments_likes_date FROM cmw_news_comments_likes WHERE news_comments_likes_id =:id";

        $db = self::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array("id" => $likeId))) {
            return null;
        }

        $res = $res->fetch();

        $user = (new UsersModel())->getUserById($res["news_comments_likes_user_id"]);

        return new NewsCommentsLikesEntity(
            $res['news_comments_likes_id'],
            $res['news_comments_likes_comments_id'],
            $user,
            $res['news_comments_likes_date'],
            $this->getTotalLikesForComments($res['news_comments_likes_id'])
        );
    }

    /**
     * @param int $commentsId
     * @return int
     * @desc Get all the likes for a specific comments
     */
    public function getTotalLikesForComments(int $commentsId): int
    {
        $sql = "SELECT news_comments_likes_comments_id FROM cmw_news_comments_likes
                                       WHERE news_comments_likes_comments_id = :comments_id";

        $db = self::getInstance();
        $req = $db->prepare($sql);
        $res = $req->execute(array("comments_id" => $commentsId));

        if ($res) {
            $lines = $req->fetchAll();

            return count($lines);
        }

        return 0;
    }

    /**
     * @param int $commentsId
     * @return \CMW\Entity\News\NewsCommentsLikesEntity|null
     */
    public function getLikesForComments(int $commentsId): ?NewsCommentsLikesEntity
    {
        $sql = "SELECT * FROM cmw_news_comments_likes WHERE news_comments_likes_comments_id = :comments_id";

        $db = self::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array("comments_id" => $commentsId))) {
            return null;
        }

        $res = $res->fetch();

        $totalLikes = $this->getTotalLikesForComments($commentsId);

        if ($res) {
            $user = (new UsersModel())->getUserById($res["news_comments_likes_user_id"]);
        }

        return new NewsCommentsLikesEntity(
            $res['news_comments_likes_id'] ?? null,
            $res['news_comments_likes_comments_id'] ?? null,
            $user ?? null,
            $res['news_comments_likes_date'] ?? null,
            $totalLikes
        );


    }


}