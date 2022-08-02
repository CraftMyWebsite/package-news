<?php

namespace CMW\Controller\News;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Menus\MenusController;
use CMW\Controller\Users\UsersController;
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

    public function __construct($themePath = null)
    {
        parent::__construct($themePath);
        $this->usersModel = new UsersModel();
        $this->newsModel = new NewsModel();
        $this->newsLikesModel = new NewsLikesModel();
    }


    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/news")]
    #[Link("/add", Link::GET, [], "/cmw-admin/news")]
    public function addNews(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.add");

        View::createAdminView('news', 'add')
            ->addScriptBefore("admin/resources/vendors/summernote/summernote.min.js", "admin/resources/vendors/summernote/summernote-bs4.min.js", "app/package/wiki/views/assets/js/summernoteInit.js")
            ->addStyle(  "admin/resources/vendors/summernote/summernote-bs4.min.css", "admin/resources/vendors/summernote/summernote.min.css")
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/news")]
    public function addNewsPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.add");

        [$title, $desc, $content] = Utils::filterInput('title', 'desc', 'content');

        $comm = filter_input(INPUT_POST, "comm") === null ? 0 : 1;
        $likes = filter_input(INPUT_POST, "likes") === null ? 0 : 1;

        $slug = Utils::normalizeForSlug($title);

        $userEntity = $this->usersModel->getUserById($_SESSION['cmwUserId']);
        $authorId = $userEntity->getId();

        $image = $_FILES['image'];

        $this->newsModel->createNews($title, $desc, $comm, $likes, $content, $slug, $authorId, $image);

        header("location: ../news/list");
    }

    #[Link("/list", Link::GET, [], "/cmw-admin/news")]
    public function listNews(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.list");

        $newsList = $this->newsModel->getNews();

        View::createAdminView('news', 'list')
            ->addVariableList(["newsList" => $newsList])
            ->view();
    }

    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNews(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        $news = $this->newsModel->getNewsById($id);

        View::createAdminView('news', 'edit')
            ->addScriptBefore("admin/resources/vendors/summernote/summernote.min.js",
                "admin/resources/vendors/summernote/summernote-bs4.min.js",
                "app/package/wiki/views/assets/js/summernoteInit.js")
            ->addStyle("admin/resources/vendors/summernote/summernote-bs4.min.css",
                "admin/resources/vendors/summernote/summernote.min.css")
            ->addVariableList(["news" => $news])
            ->view();
    }

    #[Link("/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNewsPost(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        [$title, $desc, $content] = Utils::filterInput('title', 'desc', 'content');

        $comm = filter_input(INPUT_POST, "comm") === null ? 0 : 1;
        $likes = filter_input(INPUT_POST, "likes") === null ? 0 : 1;

        $slug = Utils::normalizeForSlug($title);


        $this->newsModel->updateNews($id, $title, $desc, $comm, $likes, $content, $slug,
            ($_FILES['image']['size'] == 0 && $_FILES['image']['error'] == 0 ? null : $_FILES['image']));

        header("location: ../list");
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function deleteNews(int $id): void
    {
        $this->newsModel->deleteNews($id);

        header("location: ../list");
    }


    #[Link("/news/like/:id", Link::GET, ["id" => "[0-9]+"])]
    public function likeNews(int $newsId)
    {
        $user = $this->usersModel->getCurrentUser();
        $news = $this->newsModel->getNewsById($newsId);

        //First check if the news is likeable
        if(!$news->isLikesStatus()) {
            header('Location: ' . getenv("PATH_SUBFOLDER") . "news");
        }

        //We check if the player has already like this news, and we store the like
        if($this->newsLikesModel->userCanLike($newsId, $user->getId())) {
            $this->newsLikesModel->storeLike($newsId, $user->getId());
        }

        //Response::sendAlert("error", "Erreur", "Vous avez déjà liké cette actualité");

       header('Location: ' . getenv("PATH_SUBFOLDER") . "news");
    }




    //////// PUBLIC AREA \\\\\\\\


    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link("/news", Link::GET)]
    public function publicListNews(): void
    {
        $newsList = $this->newsModel->getNews();

        //Include the public view file ("public/themes/$themePath/views/news/list.view.php")
        $view = new View('news', 'list');
        $view->addVariableList(["newsList" => $newsList]);
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