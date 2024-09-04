<?php

namespace CMW\Controller\News;

use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\News\NewsCommentsLikesModel;
use CMW\Model\News\NewsCommentsModel;
use CMW\Model\News\NewsLikesModel;
use CMW\Model\News\NewsModel;
use CMW\Model\News\NewsTagsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;

/**
 * Class: @NewsPublicController
 * @package News
 * @author Teyir
 * @version 1.0
 */
class NewsPublicController extends AbstractController
{

    #[Link("/news", Link::GET)]
    public function publicListNews(): void
    {
        $newsList = NewsModel::getInstance()->getNews();
        $newsModel = NewsModel::getInstance();

        //Include the Public view file ("Public/Themes/$themePath/Views/News/list.view.php")
        $view = new View('News', 'list');
        $view->addScriptBefore("Admin/Resources/Vendors/Prismjs/prism.js");
        $view->addVariableList(["newsList" => $newsList, "newsModel" => $newsModel]);
        $view->view();
    }

    #[Link("/news/:tagSlug/:articleSlug", Link::GET, ["tagSlug" => ".*?", "articleSlug" => ".*?"])]
    public function publicNewsTagIndividual(Request $request, string $tagSlug, string $articleSlug): void
    {
        $news = NewsModel::getInstance()->getNewsBySlug($articleSlug);
        $tag = NewsTagsModel::getInstance()->isTagExistByName($tagSlug);

        if (is_null($news) || !$tag) {
            Redirect::redirectToHome();
        }

        NewsModel::getInstance()->incrementViews($news->getNewsId());

        $view = new View('News', 'individual');
        $view->addScriptBefore("Admin/Resources/Vendors/Prismjs/prism.js");
        $view->addVariableList(["news" => $news]);
        $view->view();
    }

    #[Link("/news/:slug", Link::GET, ["slug" => ".*?"])]
    public function publicIndividualNews(Request $request, string $slug): void
    {

        $isTag = NewsTagsModel::getInstance()->isTagExistByName($slug);

        if ($isTag) {
            $this->publicListNewsForTag($slug);
            return;
        }

        $news = NewsModel::getInstance()->getNewsBySlug($slug);

        if (is_null($news)) {
            Redirect::redirectToHome();
        }

        NewsModel::getInstance()->incrementViews($news->getNewsId());

        //Include the Public view file ("Public/Themes/$themePath/Views/News/individual.view.php")
        $view = new View('News', 'individual');
        $view->addScriptBefore("Admin/Resources/Vendors/Prismjs/prism.js");
        $view->addVariableList(["news" => $news]);
        $view->view();
    }

    public function publicListNewsForTag(string $tagSlug): void
    {
        $tag = NewsTagsModel::getInstance()->getTagByName($tagSlug);

        if (!$tag) {
            Redirect::redirectToHome();
        }

        $newsList = NewsTagsModel::getInstance()->getNewsForTagById($tag->getId());
        $newsModel = NewsModel::getInstance();

        $view = new View('News', 'list');
        $view->addScriptBefore("Admin/Resources/Vendors/Prismjs/prism.js");
        $view->addVariableList(["newsList" => $newsList, "newsModel" => $newsModel]);
        $view->view();
    }

    #[Link("/like/news/comments/:id", Link::GET, ["id" => "[0-9]+"])]
    public function likeCommentsNews(Request $request, int $commentsId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();

        //We check if the player has already like this comments, and we store the like
        if (newsCommentsLikesModel::getInstance()->userCanLike($commentsId, $user?->getId())) {
            newsCommentsLikesModel::getInstance()->storeLike($commentsId, $user?->getId());
        }

        Redirect::redirectPreviousRoute();
    }

    #[Link("/like/news/:id", Link::GET, ["id" => "[0-9]+"])]
    public function likeNews(Request $request, int $id): void
    {
        $user = usersModel::getInstance()::getCurrentUser();
        $news = NewsModel::getInstance()->getNewsById($id);

        //First check if the news is likeable
        if (!$news?->isLikesStatus()) {
            Redirect::redirect('news');
        }

        if (newsLikesModel::getInstance()->userCanLike($id, $user?->getId())) {
            newsLikesModel::getInstance()->storeLike($id, $user?->getId());
        }

        Redirect::redirectPreviousRoute();
    }

    #[Link("/news/comments/:id", Link::POST, ["id" => "[0-9]+"])]
    public function commentsNews(Request $request, int $newsId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();

        $content = strip_tags(htmlentities(filter_input(INPUT_POST, 'comments')));

        if (NewsCommentsModel::getInstance()->userCanComment($newsId, $user?->getId())) {
            newsCommentsModel::getInstance()->storeComments($newsId, $user?->getId(), $content);
        }

        Redirect::redirectPreviousRoute();
    }
}
