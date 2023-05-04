<?php

namespace CMW\Model\News;

use CMW\Entity\News\NewsBannedPlayersEntity;
use CMW\Entity\News\NewsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Images;
use JetBrains\PhpStorm\ExpectedValues;
use JsonException;


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
        try {
            $imageName = ImagesManager::upload($image, "news");
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
        } catch (JsonException) {
        }

        return null;
    }

    private function translatePage($content): string
    {
        $content = json_decode($content, false);

        $blocks = $content->blocks;
        $convertedHtml = "";
        foreach ($blocks as $block) {
            switch ($block->type) {
                case "header":
                    $level = $block->data->level;
                    $text = $block->data->text;
                    $convertedHtml .= "<h$level class='editor_h$level'>$text</h$level>";
                    break;

                case "embed":
                    $src = $block->data->embed;
                    $convertedHtml .=
                        <<<HTML
                            <div>
                                <iframe width="560" height="315" src="$src" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                            </div>
                        HTML;
                    break;

                case "paragraph":
                    $text = $block->data->text;
                    $convertedHtml .=
                        <<<HTML
                            <p class='editor_p'>$text</p>
                        HTML;
                    break;

                case "delimiter":
                    $convertedHtml .=
                        <<<HTML
                            <hr class='editor_hr'>
                        HTML;
                    break;

                case "image":
                    $src = $block->data->file->url;
                    $caption = $block->data->caption;
                    $convertedHtml .=
                        <<<HTML
                            <img class="editor_img" src="$src" title="$caption" alt="$caption" /><br /><em>$caption</em>
                        HTML;
                    break;

                case "list":
                    $convertedHtml .= ($block->data->style === "unordered") ? "<ul class='editor_ul' style='list-style-type: disc'>" : "<ol class='editor_ol' style='list-style-type: decimal'>";
                    foreach ($block->data->items as $item) {
                        $convertedHtml .=
                            <<<HTML
                                <li class='editor_li'>$item</li>
                            HTML;
                    }
                    $convertedHtml .= ($block->data->style === "unordered") ? "</ul>" : "</ol>";
                    break;

                case "quote":
                    $text = $block->data->text;
                    $caption = $block->data->caption;
                    $convertedHtml .=
                        <<<HTML
                            <figure class='editor_figure'>
                                <blockquote class='editor_blockquote'>
                                    <p class='editor_p'>$text</p> 
                                </blockquote>
                                <figcaption class='editor_figcaption'>$caption</figcaption>
                            </figure>
                        HTML;
                    break;



                case "code":
                    $text = $block->data->code;
                    $textconverted = htmlspecialchars($text, ENT_COMPAT);
                    $convertedHtml .=
                        <<<HTML
                        <div class="editor_allcode">
                            <pre class="editor_pre">
                                <code class="editor_code">$textconverted</code>
                            </pre>
                        </div>
                        HTML;
                    break;

                case "warning":
                    $title = $block->data->title;
                    $message = $block->data->message;
                    $convertedHtml .=
                        <<<HTML
                            <div class="editor_warning">
                                <div class="editor_warning-title">
                                    <p class='editor_p'>$title</p>
                                </div>
                                <div class="editor_warning-content">
                                    <p class='editor_p'>$message</p>
                                </div>
                            </div>
                        HTML;
                    break;

                case "linkTool":
                    $link = $block->data->link;
                    $convertedHtml .=
                        <<<HTML
                            <a class='editor_a' href="$link">$link</a>
                        HTML;
                    break;

                case "table":
                    $convertedHtml .= "<table class='editor_table'><tbody class='editor_tbody'>";
                    foreach ($block->data->content as $tr) {
                        $convertedHtml .= "<tr class='editor_tr'>";
                        foreach ($tr as $td) {
                            $convertedHtml .= "<td class='editor_td'>$td</td>";
                        }
                        $convertedHtml .= "</tr>";

                    }
                    $convertedHtml .= "</table></tbody>";
                    break;
            }
        }

        return $convertedHtml;
    }

    public function getNewsById(int $newsId): ?NewsEntity
    {

        $sql = "SELECT news_id, news_title, news_desc, news_comments_status, news_likes_status, news_content, 
                news_slug, news_author, news_views, news_image_name, news_date_created
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
            $this->translatePage($res["news_content"]),
            $res["news_content"],
            $res['news_slug'],
            $author,
            $res['news_views'],
            $res['news_image_name'],
            $res['news_date_created'],
            $newsLikes,
            (new NewsCommentsModel())->getCommentsForNews($res['news_id'])
        );
    }

    public function getNewsBySlug(string $newsSlug): ?NewsEntity
    {

        $sql = "SELECT news_id, news_title, news_desc, news_comments_status, news_likes_status, news_content, 
                news_slug, news_author, news_views, news_image_name, news_date_created
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
            $this->translatePage($res["news_content"]),
            $res["news_content"],
            $res['news_slug'],
            $author,
            $res['news_views'],
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

        $order === "ASC" ? $sql = "SELECT news_id FROM cmw_news ORDER BY `cmw_news`.`news_id` LIMIT :limit"
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

    public function updateNews(int $newsId, string $title, string $desc, int $comm, int $likes, string $content, string $slug, array|null $image): ?NewsEntity
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
        if (!empty($image['name'])) {
            //Delete the old image
            unlink(getenv("dir") . "Public/uploads/news/" . $this->getNewsById($newsId)?->getImageName());

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
        unlink(getenv("dir") . "Public/uploads/news/" . $this->getNewsById($newsId)?->getImageName());

        $sql = "DELETE FROM cmw_news WHERE news_id=:news_id";

        $db = self::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("news_id" => $newsId));
    }

    public function getBannedUsers(): array
    {

        $sql = "SELECT news_banned_players_player_id FROM cmw_news_banned_players";
        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($player = $res->fetch()) {
            $toReturn[] = $this->getBannedUser($player["news_banned_players_player_id"]);
        }

        return $toReturn;
    }

    public function getBannedUser(int $userId): ?NewsBannedPlayersEntity
    {

        $sql = "SELECT news_banned_players_id, news_banned_players_player_id, news_banned_players_author_id, news_banned_players_date
                FROM cmw_news_banned_players WHERE news_banned_players_player_id = :userId";

        $db = self::getInstance();
        $res = $db->prepare($sql);


        if (!$res->execute(array("userId" => $userId))) {
            return null;
        }

        $res = $res->fetch();

        $player = (new UsersModel())->getUserById($userId);
        $author = (new UsersModel())->getUserById($res['news_banned_players_author_id']);

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
            $var = array(
                "userId" => $userId,
                "authorId" => UsersModel::getCurrentUser()?->getId()
            );

            $sql = "INSERT INTO cmw_news_banned_players (news_banned_players_player_id, news_banned_players_author_id) 
                    VALUES (:userId, :authorId)";

            $db = self::getInstance();

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

        $db = self::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array("user_id" => $userId));

        return count($res->fetchAll()) !== 0;
    }

    public function incrementViews(int $newsId): void
    {
        $sql = "UPDATE cmw_news SET news_views = news_views + 1 WHERE news_id = :id";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        $req->execute(array("id" => $newsId));
    }

}
