<?php

namespace CMW\Controller\News;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\News\NewsCommentsLikesModel;
use CMW\Model\News\NewsCommentsModel;
use CMW\Model\News\NewsLikesModel;
use CMW\Model\News\NewsModel;
use CMW\Model\Users\UsersModel;
use CMW\Router\Link;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;

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
            ->addScriptBefore("admin/resources/vendors/editorjs/plugins/header.js",
                "admin/resources/vendors/editorjs/plugins/image.js",
                "admin/resources/vendors/editorjs/plugins/delimiter.js",
                "admin/resources/vendors/editorjs/plugins/list.js",
                "admin/resources/vendors/editorjs/plugins/quote.js",
                "admin/resources/vendors/editorjs/plugins/editorjs-codeflask.js",
                "admin/resources/vendors/editorjs/plugins/table.js",
                "admin/resources/vendors/editorjs/plugins/link.js",
                "admin/resources/vendors/editorjs/plugins/warning.js",
                "admin/resources/vendors/editorjs/plugins/embed.js",
                "admin/resources/vendors/editorjs/plugins/marker.js",
                "admin/resources/vendors/editorjs/plugins/underline.js",
                "admin/resources/vendors/editorjs/plugins/drag-drop.js",
                "admin/resources/vendors/editorjs/plugins/undo.js",
                "admin/resources/vendors/editorjs/editor.js")
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
        ->addStyle("admin/resources/vendors/simple-datatables/style.css","admin/resources/assets/css/pages/simple-datatables.css","admin/resources/vendors/summernote/summernote-lite.css","admin/resources/assets/css/pages/summernote.css")
        ->addScriptAfter("admin/resources/vendors/simple-datatables/umd/simple-datatables.js","admin/resources/assets/js/pages/simple-datatables.js","admin/resources/vendors/jquery/jquery.min.js","admin/resources/vendors/summernote/summernote-lite.min.js","admin/resources/assets/js/pages/summernote.js")
            ->addVariableList(["newsList" => $newsList])
            ->view();
    }

    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNews(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        $news = $this->newsModel->getNewsById($id);

        View::createAdminView('news', 'edit')
            ->addScriptBefore("admin/resources/vendors/editorjs/plugins/header.js",
                "admin/resources/vendors/editorjs/plugins/image.js",
                "admin/resources/vendors/editorjs/plugins/delimiter.js",
                "admin/resources/vendors/editorjs/plugins/list.js",
                "admin/resources/vendors/editorjs/plugins/quote.js",
                "admin/resources/vendors/editorjs/plugins/editorjs-codeflask.js",
                "admin/resources/vendors/editorjs/plugins/table.js",
                "admin/resources/vendors/editorjs/plugins/link.js",
                "admin/resources/vendors/editorjs/plugins/warning.js",
                "admin/resources/vendors/editorjs/plugins/embed.js",
                "admin/resources/vendors/editorjs/plugins/marker.js",
                "admin/resources/vendors/editorjs/plugins/underline.js",
                "admin/resources/vendors/editorjs/plugins/drag-drop.js",
                "admin/resources/vendors/editorjs/plugins/undo.js",
                "admin/resources/vendors/editorjs/editor.js")
            ->addVariableList(["news" => $news])
            ->view();
    }

    #[Link("/edit", Link::POST, [], "/cmw-admin/news", secure: false)]
    public function editNewsPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        [$id, $title, $desc, $content, $comm, $likes] = Utils::filterInput('id', 'title', 'desc', 'content', 'comm', 'likes');

        $slug = Utils::normalizeForSlug($title);

        if (isset($_FILES['image'])){
            $image = $this->newsModel->getNewsById($id)?->getImageName();
        } else {
            $image = $_FILES['image'];
        }

        
        $this->newsModel->updateNews($id, $title, $desc, $comm, $likes, $content, $slug, $image);
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function deleteNews(int $id): void
    {
        $this->newsModel->deleteNews($id);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("news.delete.toasters.success"));

        header("location: ../manage");
    }

    #[Link("/news/like/comment/:id", Link::GET, ["id" => "[0-9]+"])]
    public function likeCommentsNews(int $commentsId): void
    {
        $user = $this->usersModel::getCurrentUser();


        //We check if the player has already like this comments, and we store the like
        if ($this->newsCommentsLikesModel->userCanLike($commentsId, $user?->getId())) {
            $this->newsCommentsLikesModel->storeLike($commentsId, $user?->getId());
        }

        //Response::sendAlert("error", "Erreur", "Vous avez déjà liké ce commentaire");

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/news/like/:id", Link::GET, ["id" => "[0-9]+"])]
    public function likeNews(int $newsId): void
    {
        $user = $this->usersModel::getCurrentUser();
        $news = $this->newsModel->getNewsById($newsId);

        //First check if the news is likeable
        if (!$news?->isLikesStatus()) {
            header('Location: ' . getenv("PATH_SUBFOLDER") . "news");
        }

        //We check if the player has already like this news, and we store the like
        if ($this->newsLikesModel->userCanLike($newsId, $user?->getId())) {
            $this->newsLikesModel->storeLike($newsId, $user?->getId());
        }

        //Response::sendAlert("error", "Erreur", "Vous avez déjà liké cette actualité");

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/news/comments/:id", Link::POST, ["id" => "[0-9]+"])]
    public function commentsNews(int $newsId): void
    {
        $user = $this->usersModel::getCurrentUser();

        $content = strip_tags(htmlentities(filter_input(INPUT_POST, 'comments')));

        if((new NewsCommentsModel())->userCanComment($newsId, $user?->getId()))
        {
            $this->newsCommentsModel->storeComments($newsId, $user?->getId(), $content);
        }

        header("location: " . $_SERVER['HTTP_REFERER']);
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

        //Include the public view file ("public/themes/$themePath/views/news/list.view.php")
        $view = new View('news', 'list');
        $view->addScriptBefore("admin/resources/vendors/highlight/highlight.min.js","admin/resources/vendors/highlight/highlightAll.js");
        $view->addStyle("admin/resources/vendors/highlight/rainbow.css");//Can be a choice
        $view->addVariableList(["newsList" => $newsList, "newsModel" => $newsModel]);
        $view->view();
    }

    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link("/news/:slug", Link::GET, ["slug" => ".*?"])]
    public function publicIndividualNews(string $slug): void
    {
        $news = $this->newsModel->getNewsBySlug($slug);

        if (!is_null($news)){
            $this->newsModel->incrementViews($news->getNewsId());
        }

        //Include the public view file ("public/themes/$themePath/views/news/individual.view.php")
        $view = new View('news', 'individual');
        $view->addScriptBefore("admin/resources/vendors/highlight/highlight.min.js","admin/resources/vendors/highlight/highlightAll.js");
        $view->addStyle("admin/resources/vendors/highlight/rainbow.css");//Can be a choice
        $view->addVariableList(["news" => $news]);
        $view->view();
    }

}