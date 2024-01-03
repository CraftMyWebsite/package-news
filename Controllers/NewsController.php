<?php

namespace CMW\Controller\News;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\News\NewsModel;
use CMW\Model\News\NewsTagsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;

/**
 * Class: @NewsController
 * @package News
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

        $tags = NewsTagsModel::getInstance()->getTags();

        View::createAdminView('News', 'add')
            ->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js",
                "Admin/Resources/Vendors/Tinymce/Config/full.js")
            ->addVariableList(['tags' => $tags])
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/news")]
    public function addNewsPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.add");

        [$title, $desc, $content, $comm, $likes] = Utils::filterInput("title", "desc", "content", "comm", "likes");

        $slug = Utils::normalizeForSlug(filter_input(INPUT_POST, "title"));
        $userId = UsersModel::getCurrentUser()?->getId();
        $image = $_FILES['image'];

        $news = NewsModel::getInstance()->createNews($title, $desc, $comm, $likes, $content, $slug, $userId, $image);

        if (is_null($news)) {
            //TODO: Why not save $content in localStorage or $_SESSION ?

            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("news.add.toasters.error"));
            Redirect::redirectPreviousRoute();
        }

        //Add tags
        if (isset($_POST['tags']) && $_POST['tags'] !== []) {
            foreach ($_POST['tags'] as $tag) {
                NewsTagsModel::getInstance()->addTagToNews($tag, $news->getNewsId());
            }
        }

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("news.add.toasters.success"));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/manage", Link::GET, [], "/cmw-admin/news")]
    public function listNews(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage");

        $newsList = NewsModel::getInstance()->getNews();
        $tags = NewsTagsModel::getInstance()->getTags();

        View::createAdminView('News', 'manage')
            /*El famosso doublon*/
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css", "Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js", "Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->addVariableList(["newsList" => $newsList, 'tags' => $tags])
            ->view();
    }

    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNews(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        $news = NewsModel::getInstance()->getNewsById($id);
        $tags = NewsTagsModel::getInstance()->getTags();

        View::createAdminView('News', 'edit')
            ->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js",
                "Admin/Resources/Vendors/Tinymce/Config/full.js")
            ->addVariableList(["news" => $news, 'tags' => $tags])
            ->view();
    }

    #[Link("/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNewsPost(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.edit");

        [$title, $desc, $content, $comm, $likes] = Utils::filterInput('title', 'desc', 'content', 'comm', 'likes');

        $slug = Utils::normalizeForSlug($title);

        $image = $_FILES['image'];

        NewsModel::getInstance()->updateNews($id, $title, $desc, $comm, $likes, $content, $slug, $image);

        //Clear and add tags
        if (isset($_POST['tags']) && $_POST['tags'] !== []) {
            NewsTagsModel::getInstance()->clearTagsForANews($id);
            foreach ($_POST['tags'] as $tag) {
                NewsTagsModel::getInstance()->addTagToNews($tag, $id);
            }
        }

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("news.edit.toasters.success"));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function deleteNews(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.delete");

        NewsModel::getInstance()->deleteNews($id);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("news.delete.toasters.success"));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/tag", Link::POST, [], "/cmw-admin/news")]
    public function addNewsTagPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage");

        $name = FilterManager::filterInputStringPost('name');
        $icon = FilterManager::filterInputStringPost('icon', orElse: null);
        $color = FilterManager::filterInputStringPost('color', orElse: null);

        if (NewsTagsModel::getInstance()->createTag($name, $icon, $color)) {
            Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
                LangManager::translate("news.tags.toasters.add.success"));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("news.tags.toasters.add.error"));
        }
        Redirect::redirectPreviousRoute();
    }

    #[Link("/tag/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function deleteNewsTag(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage");

        if (NewsTagsModel::getInstance()->deleteTag($id)) {
            Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
                LangManager::translate("news.tags.toasters.delete.success"));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("news.tags.toasters.delete.error"));
        }
        Redirect::redirectPreviousRoute();
    }

    #[Link("/tag/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNewsTag(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage");

        $tag = NewsTagsModel::getInstance()->getTagById($id);

        View::createAdminView('News', 'Tags/edit')
            ->addVariableList(["tag" => $tag])
            ->view();
    }

    #[Link("/tag/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNewsTagPost(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage");

        $name = FilterManager::filterInputStringPost('name');
        $icon = FilterManager::filterInputStringPost('icon', orElse: null);
        $color = FilterManager::filterInputStringPost('color', orElse: null);

        if (NewsTagsModel::getInstance()->editTag($id, $name, $icon, $color)) {
            Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
                LangManager::translate("news.tags.toasters.edit.success"));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("news.tags.toasters.edit.error"));
        }
        Redirect::redirectToAdmin('news/manage');
    }
}