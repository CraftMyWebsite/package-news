<?php

namespace CMW\Controller\News;

use CMW\Controller\Core\EditorController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\News\NewsCommentsLikesModel;
use CMW\Model\News\NewsCommentsModel;
use CMW\Model\News\NewsLikesModel;
use CMW\Model\News\NewsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;

/**
 * Class: @NewsController
 * @package news
 * @author Teyir
 * @version 1.0
 */
class NewsController extends AbstractController
{
    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/news")]
    #[Link("/add", Link::GET, [], "/cmw-admin/news")]
    public function addNews(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.add");

        View::createAdminView('News', 'add')
            ->addScriptBefore("Admin/Resources/Vendors/Editorjs/Plugins/header.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/image.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/delimiter.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/list.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/quote.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/code.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/table.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/link.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/warning.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/embed.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/marker.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/underline.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/drag-drop.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/undo.js",
                "Admin/Resources/Vendors/Editorjs/editor.js")
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/news", secure: false)]
    public function addNewsPost(): void
    {

        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.add");

        $user = new UsersModel();

        [$title, $desc, $content, $comm, $likes] = Utils::filterInput("title", "desc", "content", "comm", "likes");

        $slug = Utils::normalizeForSlug(filter_input(INPUT_POST, "title"));
        $userId = $user::getLoggedUser();
        $image = $_FILES['image'];

        newsModel::getInstance()->createNews($title, $desc, $comm, $likes, $content, $slug, $userId, $image);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("news.add.toasters.success"));

    }

    #[Link("/manage", Link::GET, [], "/cmw-admin/news")]
    public function listNews(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage");

        $newsList = newsModel::getInstance()->getNews();

        View::createAdminView('News', 'manage')
            /*El famosso doublon*/
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css", "Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js", "Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->addVariableList(["newsList" => $newsList])
            ->view();
    }

    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNews(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        $news = newsModel::getInstance()->getNewsById($id);

        View::createAdminView('News', 'edit')
            ->addScriptBefore("Admin/Resources/Vendors/Editorjs/Plugins/header.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/image.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/delimiter.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/list.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/quote.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/code.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/table.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/link.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/warning.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/embed.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/marker.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/underline.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/drag-drop.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/undo.js",
                "Admin/Resources/Vendors/Editorjs/editor.js")
            ->addVariableList(["news" => $news])
            ->view();
    }

    #[Link("/edit", Link::POST, [], "/cmw-admin/news", secure: false)]
    public function editNewsPost(): void
    {

        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        [$id, $title, $desc, $content, $comm, $likes] = Utils::filterInput('id', 'title', 'desc', 'content', 'comm', 'likes');

        $slug = Utils::normalizeForSlug($title);

        $image = $_FILES['image'];

        newsModel::getInstance()->updateNews($id, $title, $desc, $comm, $likes, $content, $slug, $image);
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function deleteNews(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.delete");

        newsModel::getInstance()->deleteNews($id);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("news.delete.toasters.success"));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/news/like/comment/:id", Link::GET, ["id" => "[0-9]+"])]
    public function likeCommentsNews(Request $request, int $commentsId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();


        //We check if the player has already like this comments, and we store the like
        if (newsCommentsLikesModel::getInstance()->userCanLike($commentsId, $user?->getId())) {
            newsCommentsLikesModel::getInstance()->storeLike($commentsId, $user?->getId());
        }

        Redirect::redirectPreviousRoute();
    }

    #[Link("/news/like/:id", Link::GET, ["id" => "[0-9]+"])]
    public function likeNews(Request $request, int $newsId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();
        $news = newsModel::getInstance()->getNewsById($newsId);

        //First check if the news is likeable
        if (!$news?->isLikesStatus()) {
            Redirect::redirect('news');
        }

        if (newsLikesModel::getInstance()->userCanLike($newsId, $user?->getId())) {
            newsLikesModel::getInstance()->storeLike($newsId, $user?->getId());
        }

        Redirect::redirectPreviousRoute();
    }

    #[Link("/news/comments/:id", Link::POST, ["id" => "[0-9]+"])]
    public function commentsNews(Request $request, int $newsId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();

        $content = strip_tags(htmlentities(filter_input(INPUT_POST, 'comments')));

        if ((new NewsCommentsModel())->userCanComment($newsId, $user?->getId())) {
            newsCommentsModel::getInstance()->storeComments($newsId, $user?->getId(), $content);
        }

        Redirect::redirectPreviousRoute();
    }


    //////// PUBLIC AREA \\\\\\\\


    #[Link("/news", Link::GET)]
    public function publicListNews(): void
    {
        $newsList = newsModel::getInstance()->getNews();
        $newsModel = newsModel::getInstance();

        //Include the Public view file ("Public/Themes/$themePath/Views/News/list.view.php")
        $view = new View('News', 'list');
        $view->addScriptBefore("Admin/Resources/Vendors/Highlight/highlight.min.js", "Admin/Resources/Vendors/Highlight/highlightAll.js");
        $view->addStyle("Admin/Resources/Vendors/Highlight/Style/" . EditorController::getCurrentStyle());
        $view->addVariableList(["newsList" => $newsList, "newsModel" => $newsModel]);
        $view->view();
    }


    #[Link("/news/:slug", Link::GET, ["slug" => ".*?"])]
    public function publicIndividualNews(Request $request, string $slug): void
    {
        $news = newsModel::getInstance()->getNewsBySlug($slug);

        if (!is_null($news)) {
            newsModel::getInstance()->incrementViews($news->getNewsId());
        }

        //Include the Public view file ("Public/Themes/$themePath/Views/News/individual.view.php")
        $view = new View('News', 'individual');
        $view->addScriptBefore("Admin/Resources/Vendors/Highlight/highlight.min.js", "Admin/Resources/Vendors/Highlight/highlightAll.js");
        $view->addStyle("Admin/Resources/Vendors/Highlight/Style/" . EditorController::getCurrentStyle());
        $view->addVariableList(["news" => $news]);
        $view->view();
    }

}