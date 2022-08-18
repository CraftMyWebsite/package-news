<?php

namespace CMW\Model\News;

use CMW\Entity\News\NewsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Images;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\ExpectedValues;


/**
 * Class @NewsModel
 * @package news
 * @author Teyir
 * @version 1.0
 */
class NewsModel extends DatabaseManager
{

    public function createNews(string $title, string $desc, int $comm, int $likes, string $content, string $slug, int $authorId, array $image): ?NewsEntity
    {

        //Upload image
        $imageName = Images::upload($image, "news");

        $var = array(
            'title' => $title,
            'desc' => $desc,
            'comm' => $comm,
            'likes' => $likes,
            'content' => $content,
            'slug' => $slug,
            'authorId' => $authorId,
            'imageName' => $imageName
        );

        $sql = "INSERT INTO cmw_news (news_title, news_desc, news_comments_status, news_likes_status, news_content, 
                news_slug, news_author, news_image_name) 
                VALUES (:title, :desc, :comm, :likes, :content, :slug, :authorId, :imageName)";

        $db = self::getInstance();
        $req = $db->prepare($sql);


        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            return $this->getNewsById($id);
        }

        return null;
    }

    public function getNewsById(int $newsId): ?NewsEntity
    {

        $sql = "SELECT news_id, news_title, news_desc, news_comments_status, news_likes_status, news_content, 
                news_slug, news_author, news_image_name, 
                DATE_FORMAT(news_date_created, '%d/%m/%Y à %H:%i:%s') AS 'news_date_created' 
                FROM cmw_news WHERE news_id=:news_id";

        $db = self::getInstance();
        $res = $db->prepare($sql);


        if (!$res->execute(array("news_id" => $newsId))) {
            return null;
        }

        $res = $res->fetch();

        $author = (new UsersModel())->getUserById($res["news_author"]);
        $newsLikes = (new NewsLikesModel())->getLikesForNews($res['news_id']);

        return new NewsEntity(
            $res['news_id'],
            $res['news_title'],
            $res['news_desc'],
            $res['news_comments_status'],
            $res['news_likes_status'],
            $res['news_content'],
            $res['news_slug'],
            $author,
            $res['news_image_name'],
            $res['news_date_created'],
            $newsLikes,
            (new NewsCommentsModel())->getCommentsForNews($res['news_id'])
        );
    }

    public function getNewsBySlug(string $newsSlug): ?NewsEntity
    {

        $sql = "SELECT news_id, news_title, news_desc, news_comments_status, news_likes_status, news_content, 
                news_slug, news_author, news_image_name, 
                DATE_FORMAT(news_date_created, '%d/%m/%Y à %H:%i:%s') AS 'news_date_created' 
                FROM cmw_news WHERE news_slug=:news_slug";

        $db = self::getInstance();
        $res = $db->prepare($sql);


        if (!$res->execute(array("news_slug" => $newsSlug))) {
            return null;
        }

        $res = $res->fetch();

        $author = (new UsersModel())->getUserById($res["news_author"]);
        $newsLikes = (new NewsLikesModel())->getLikesForNews($res['news_id']);
        $newsComments = (new NewsCommentsModel())->getCommentsForNews($res['news_id']);

        return new NewsEntity(
            $res['news_id'],
            $res['news_title'],
            $res['news_desc'],
            $res['news_comments_status'],
            $res['news_likes_status'],
            $res['news_content'],
            $res['news_slug'],
            $author,
            $res['news_image_name'],
            $res['news_date_created'],
            $newsLikes,
            $newsComments
        );
    }

    /**
     * @return array
     * @desc return all news
     */
    public function getNews(): array
    {
        $sql = "SELECT news_id FROM cmw_news";
        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($news = $res->fetch()) {
            $toReturn[] = $this->getNewsById($news["news_id"]);
        }

        return $toReturn;
    }

    public function getSomeNews(int $limit, #[ExpectedValues (values: ['DESC', 'ASC'])] string $order = "DESC"): array
    {

        $order === "ASC" ? $sql = "SELECT news_id FROM cmw_news ORDER BY `cmw_news`.`news_id` ASC LIMIT :limit"
            : $sql = "SELECT news_id FROM cmw_news ORDER BY `cmw_news`.`news_id` DESC LIMIT :limit";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("limit" => $limit))) {
            return array();
        }

        $toReturn = array();

        while ($news = $res->fetch()) {
            $toReturn[] = $this->getNewsById($news["news_id"]);
        }

        return $toReturn;
    }

    public function updateNews(int $newsId, string $title, string $desc, bool $comm, bool $likes, string $content, string $slug, array|null $image): ?NewsEntity
    {

        $var = array(
            'newsId' => $newsId,
            'title' => $title,
            'desc' => $desc,
            'comm' => $comm,
            'likes' => $likes,
            'content' => $content,
            'slug' => $slug
        );

        $sql = "UPDATE cmw_news SET news_title = :title, news_desc = :desc, news_comments_status = :comm, 
                    news_likes_status = :likes, news_content = :content, news_slug = :slug WHERE news_id = :newsId";

        //Detect if we update the image
        if(!empty($image['name'])){
            //Delete the old image
            unlink(getenv("dir") . "public/uploads/news/" . self::getNewsById($newsId)->getImageName());

            //Upload the new image
            $imageName = Images::upload($image, "news");

            //Add the image to the var
            $var += array("imageName" => $imageName);

            //Update SQL
            $sql = "UPDATE cmw_news SET news_title = :title, news_desc = :desc, news_comments_status = :comm, 
                    news_likes_status = :likes, news_content = :content, news_slug = :slug, news_image_name = :imageName WHERE news_id = :newsId";
        }


        $db = self::getInstance();
        $req = $db->prepare($sql);
        if ($req->execute($var)) {
            return $this->getNewsById($newsId);
        }

        return null;
    }

    public function deleteNews(int $newsId): void
    {
        //Delete the image file
        unlink(getenv("dir") . "public/uploads/news/" . self::getNewsById($newsId)->getImageName());

        $sql = "DELETE FROM cmw_news WHERE news_id=:news_id";

        $db = self::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("news_id" => $newsId));
    }

}
