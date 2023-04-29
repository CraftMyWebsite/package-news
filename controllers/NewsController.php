<?php

namespace CMW\Controller\News;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\EditorController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Model\News\NewsCommentsLikesModel;
use CMW\Model\News\NewsCommentsModel;
use CMW\Model\News\NewsLikesModel;
use CMW\Model\News\NewsModel;
use CMW\Model\Users\UsersModel;
use CMW\Router\Link;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;
use CMW\Utils\Redirect;

/**
 * Class: @NewsController
 * @package news
 * @author Teyir
 * @version 1.0
 */
class NewsController extends CoreController
{
    public static string $themePath;
    private UsersModel $usersModel;
    private NewsModel $newsModel;
    private NewsLikesModel $newsLikesModel;
    private NewsCommentsModel $newsCommentsModel;
    private NewsCommentsLikesModel $newsCommentsLikesModel;

    public function __construct($themePath = null)
    {
        parent::__construct($themePath);
        $this->usersModel = new UsersModel();
        $this->newsModel = new NewsModel();
        $this->newsLikesModel = new NewsLikesModel();
        $this->newsCommentsModel = new NewsCommentsModel();
        $this->newsCommentsLikesModel = new NewsCommentsLikesModel();
    }


    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/news")]
    #[Link("/add", Link::GET, [], "/cmw-admin/news")]
    public function addNews(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.add");

        View::createAdminView('news', 'add')
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

        $this->newsModel->createNews($title, $desc, $comm, $likes, $content, $slug, $userId, $image);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),LangManager::translate("news.add.toasters.success"));

    }

    #[Link("/manage", Link::GET, [], "/cmw-admin/news")]
    public function listNews(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage");

        $newsList = $this->newsModel->getNews();

        View::createAdminView('news', 'manage')
        /*El famosso doublon*/
        ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css","Admin/Resources/Vendors/Summernote/summernote-lite.css","Admin/Resources/Assets/Css/Pages/summernote.css")
        ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js","Admin/Resources/Vendors/jquery/jquery.min.js","Admin/Resources/Vendors/Summernote/summernote-lite.min.js","Admin/Resources/Assets/Js/Pages/summernote.js")
            ->addVariableList(["newsList" => $newsList])
            ->view();
    }

    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNews(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        $news = $this->newsModel->getNewsById($id);

        View::createAdminView('news', 'edit')
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
        
        $this->newsModel->updateNews($id, $title, $desc, $comm, $likes, $content, $slug, $image);
        
        
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function deleteNews(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.delete");

        $this->newsModel->deleteNews($id);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("news.delete.toasters.success"));

        Redirect::redirectToPreviousPage();
    }

    #[Link("/news/like/comment/:id", Link::GET, ["id" => "[0-9]+"])]
    public function likeCommentsNews(Request $request, int $commentsId): void
    {
        $user = $this->usersModel::getCurrentUser();


        //We check if the player has already like this comments, and we store the like
        if ($this->newsCommentsLikesModel->userCanLike($commentsId, $user?->getId())) {
            $this->newsCommentsLikesModel->storeLike($commentsId, $user?->getId());
        }

        Redirect::redirectToPreviousPage();
    }

    #[Link("/news/like/:id", Link::GET, ["id" => "[0-9]+"])]
    public function likeNews(Request $request, int $newsId): void
    {
        $user = $this->usersModel::getCurrentUser();
        $news = $this->newsModel->getNewsById($newsId);

        //First check if the news is likeable
        if (!$news?->isLikesStatus()) {
            header('Location: ' . getenv("PATH_SUBFOLDER") . "news");
        }

        if ($this->newsLikesModel->userCanLike($newsId, $user?->getId())) {
            $this->newsLikesModel->storeLike($newsId, $user?->getId());
        }

        Redirect::redirectToPreviousPage();
    }

    #[Link("/news/comments/:id", Link::POST, ["id" => "[0-9]+"])]
    public function commentsNews(Request $request, int $newsId): void
    {
        $user = $this->usersModel::getCurrentUser();

        $content = strip_tags(htmlentities(filter_input(INPUT_POST, 'comments')));

        if((new NewsCommentsModel())->userCanComment($newsId, $user?->getId()))
        {
            $this->newsCommentsModel->storeComments($newsId, $user?->getId(), $content);
        }

        Redirect::redirectToPreviousPage();
    }




    //////// PUBLIC AREA \\\\\\\\


    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link("/news", Link::GET)]
    public function publicListNews(): void
    {
        $newsList = $this->newsModel->getNews();
        $newsModel = $this->newsModel;

        //Include the Public view file ("Public/Themes/$themePath/Views/news/list.view.php")
        $view = new View('news', 'list');
        $view->addScriptBefore("Admin/Resources/Vendors/highlight/highlight.min.js","Admin/Resources/Vendors/highlight/highlightAll.js");
        $view->addStyle("Admin/Resources/Vendors/highlight/style/" . EditorController::getCurrentStyle());
        $view->addVariableList(["newsList" => $newsList, "newsModel" => $newsModel]);
        $view->view();
    }

    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link("/news/:slug", Link::GET, ["slug" => ".*?"])]
    public function publicIndividualNews(Request $request, string $slug): void
    {
        $news = $this->newsModel->getNewsBySlug($slug);

        if (!is_null($news)){
            $this->newsModel->incrementViews($news->getNewsId());
        }

        //Include the Public view file ("Public/Themes/$themePath/Views/news/individual.view.php")
        $view = new View('news', 'individual');
        $view->addScriptBefore("Admin/Resources/Vendors/highlight/highlight.min.js","Admin/Resources/Vendors/highlight/highlightAll.js");
        $view->addStyle("Admin/Resources/Vendors/highlight/style/" . EditorController::getCurrentStyle());
        $view->addVariableList(["news" => $news]);
        $view->view();
    }

}