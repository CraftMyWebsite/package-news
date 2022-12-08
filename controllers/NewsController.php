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
use CMW\Utils\View;

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

        View::createAdminView('news', 'add')//Fait gaffe j'ai mis en double tout les appel des include comme sa t'as pas a t'embeter pour les appeler quand tu fera l'appel pour tout afficher dans "manage" le doublon se trouve un peut plus bas ligne 85 par ce que je sais pas lequel tu garde, ligne 99-100 tu peut laisser c'est pour summernote en edit.
            ->addStyle("admin/resources/vendors/simple-datatables/style.css","admin/resources/assets/css/pages/simple-datatables.css","admin/resources/vendors/summernote/summernote-lite.css","admin/resources/assets/css/pages/summernote.css")
            ->addScriptAfter("admin/resources/vendors/simple-datatables/umd/simple-datatables.js","admin/resources/assets/js/pages/simple-datatables.js","admin/resources/vendors/jquery/jquery.min.js","admin/resources/vendors/summernote/summernote-lite.min.js","admin/resources/assets/js/pages/summernote.js")
            ->view();
    }

    #[Link("/manage", Link::POST, [], "/cmw-admin/news")]
    public function addNewsPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.add");

        [$title, $desc, $content] = Utils::filterInput('title', 'desc', 'content');

        $comm = filter_input(INPUT_POST, "comm") === null ? 0 : 1;
        $likes = filter_input(INPUT_POST, "likes") === null ? 0 : 1;

        $slug = Utils::normalizeForSlug($title);

        $userEntity = $this->usersModel->getUserById($_SESSION['cmwUserId']);
        $authorId = $userEntity?->getId();

        $image = $_FILES['image'];

        $this->newsModel->createNews($title, $desc, $comm, $likes, $content, $slug, $authorId, $image);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("news.add.toasters.success"));

        header("location: manage");
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
            ->addStyle("admin/resources/vendors/summernote/summernote-lite.css","admin/resources/assets/css/pages/summernote.css")
            ->addScriptAfter("admin/resources/vendors/jquery/jquery.min.js","admin/resources/vendors/summernote/summernote-lite.min.js","admin/resources/assets/js/pages/summernote.js")
            ->addVariableList(["news" => $news])
            ->view();
    }

    #[Link("/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNewsPost(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        [$title, $desc, $content] = Utils::filterInput('title', 'desc', 'content');

        $comm = is_null(filter_input(INPUT_POST, "comm")) ? 0 : 1;
        $likes = is_null(filter_input(INPUT_POST, "likes")) ? 0 : 1;

        $slug = Utils::normalizeForSlug($title);

        $this->newsModel->updateNews($id, $title, $desc, $comm, $likes, $content, $slug,
            ($_FILES['image']['size'] === 0 && $_FILES['image']['error'] === 0 ? null : $_FILES['image']));

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("news.edit.toasters.success", ["actu" => $title]));

        header("location: ../manage");
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

        //Include the public view file ("public/themes/$themePath/views/news/individual.view.php")
        $view = new View('news', 'individual');
        $view->addVariableList(["news" => $news]);
        $view->view();
    }

}