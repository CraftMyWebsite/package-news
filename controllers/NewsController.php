<?php

namespace CMW\Controller\News;

use CMW\Controller\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Model\Faq\NewsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Utils;

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

    public function __construct($themePath = null)
    {
        parent::__construct($themePath);
        $this->usersModel = new UsersModel();
        $this->newsModel = new NewsModel();
    }

    public function addNews(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.add");

        $includes = array(
            "scripts" => [
                "before" => [
                    "admin/resources/vendors/summernote/summernote.min.js",
                    "admin/resources/vendors/summernote/summernote-bs4.min.js",
                    "app/package/wiki/views/assets/js/summernoteInit.js"
                ]
            ],
            "styles" => [
                "admin/resources/vendors/summernote/summernote-bs4.min.css",
                "admin/resources/vendors/summernote/summernote.min.css"
            ]
        );

        view('news', 'add.admin', [], 'admin', $includes);
    }

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

        header("location: ../../list");
    }

    public function listNews(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.list");

        $newsList = $this->newsModel->getNews();

        view('news', 'list.admin', ["newsList" => $newsList], 'admin', []);
    }

    public function editNews(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        $includes = array(
            "scripts" => [
                "before" => [
                    "admin/resources/vendors/summernote/summernote.min.js",
                    "admin/resources/vendors/summernote/summernote-bs4.min.js",
                    "app/package/wiki/views/assets/js/summernoteInit.js"
                ]
            ],
            "styles" => [
                "admin/resources/vendors/summernote/summernote-bs4.min.css",
                "admin/resources/vendors/summernote/summernote.min.css"
            ]
        );

        $news = $this->newsModel->getNewsById($id);

        view('news', 'edit.admin', ["news" => $news], 'admin', $includes);
    }

    public function editNewsPost(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        [$title, $desc, $content] = Utils::filterInput('title', 'desc', 'content');

        $comm = filter_input(INPUT_POST, "comm") === null ? 0 : 1;
        $likes = filter_input(INPUT_POST, "likes") === null ? 0 : 1;

        $slug = Utils::normalizeForSlug($title);


        $this->newsModel->updateNews($id, $title, $desc, $comm, $likes, $content, $slug,
            ($_FILES['image']['size'] == 0 && $_FILES['image']['error'] == 0 ? null : $_FILES['image']));

        header("location: ../../../list");
    }

    public function deleteNews(int $id): void
    {
        $this->newsModel->deleteNews($id);

        header("location: ../../list");
    }

}