<?php

namespace CMW\Model\News;

use CMW\Entity\News\NewsCommentsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Model\Users\UsersModel;


/**
 * Class @NewsCommentsModel
 * @package news
 * @author Teyir
 * @version 1.0
 */
class NewsCommentsModel extends DatabaseManager
{

    /**
     * @param int $newsId
     * @return \CMW\Entity\News\NewsCommentsEntity[]
     * @desc Get the commentsEntity
     */
    public function getCommentsForNews(int $newsId): array
    {
        $sql = "SELECT * FROM cmw_news_comments WHERE news_comments_news_id = :news_id";

        $db = self::getInstance();
        $res = $db->prepare($sql);


        if (!$res->execute(array("news_id" => $newsId))) {
            return array();
        }

        $toReturn = array();

        while ($comments = $res->fetch()) {
            $toReturn[] = $this->getCommentsById($comments["news_comments_id"]);
        }

        return $toReturn;
    }

    public function getCommentsById(int $commentsId): ?NewsCommentsEntity
    {

        $sql = "SELECT news_comments_id, news_comments_news_id, news_comments_content, news_comments_user_id,
                    DATE_FORMAT(news_comments_date, '%d/%m/%Y à %H:%i:%s') AS 'news_comments_date'
                    FROM cmw_news_comments WHERE news_comments_id =:id";

        $db = self::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array("id" => $commentsId))) {
            return null;
        }

        $res = $res->fetch();

        $user = (new UsersModel())->getUserById($res["news_comments_user_id"]);

        return new NewsCommentsEntity(
            $res['news_comments_id'],
            $res['news_comments_news_id'],
            $user,
            $res['news_comments_content'],
            $res['news_comments_date'],
            (new NewsCommentsLikesModel())->getLikesForComments($res['news_comments_id'])
        );
    }


    /**
     * @param int $newsId
     * @param int $userId
     * @param string $content
     * @return \CMW\Entity\News\NewsCommentsEntity|null
     * @Desc Store the comments
     */
    public function storeComments(int $newsId, int $userId, string $content): ?NewsCommentsEntity
    {
        $sql = "INSERT INTO cmw_news_comments (news_comments_news_id, news_comments_user_id, news_comments_content) 
                    VALUES (:news_id, :user_id, :content)";

        $db = self::getInstance();
        $res = $db->prepare($sql);


        if ($res->execute(array("news_id" => $newsId, "user_id" => $userId, "content" => $content))) {
            $id = $db->lastInsertId();
            return $this->getCommentsById($id);
        }

        return null;
    }

    public function userCanComment(int $newsId, ?int $userId): bool
    {
        if ($userId === null){
            return  false;
        }

        if((new NewsModel())->isUserBanned($userId)){
            return false;
        }


        $sql = "SELECT news_id FROM `cmw_news` 
                              WHERE news_id = :news_id AND news_comments_status = 1";

        $db = self::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array("news_id" => $newsId));

        return count($res->fetchAll()) === 0;
    }


}