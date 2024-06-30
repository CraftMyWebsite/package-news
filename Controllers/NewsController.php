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
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage.add");

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
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage.add");

        [$title, $desc, $content, $comm, $likes] = Utils::filterInput("title", "desc", "content", "comm", "likes");

        //We are storing $content for prevent error and not loose all data.
        $_SESSION['cmwNewsContent'] = $content;

        if (is_null($comm)) { $comm = 0;}
        if (is_null($likes)) { $likes = 0;}

        $slug = Utils::normalizeForSlug(FilterManager::filterInputStringPost('title'));
        $userId = UsersModel::getCurrentUser()?->getId();
        $image = $_FILES['image'];

        $news = NewsModel::getInstance()->createNews($title, $desc, $comm, $likes, $content, $slug, $userId, $image);

        if (is_null($news)) {
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

        //If all good, we reset temp session data
        $_SESSION['cmwNewsContent'] = "";

        Redirect::redirectPreviousRoute();
    }

    #[Link("/manage", Link::GET, [], "/cmw-admin/news")]
    public function listNews(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage");

        $newsList = NewsModel::getInstance()->getNews();
        $tags = NewsTagsModel::getInstance()->getTags();

        View::createAdminView('News', 'manage')
            ->addStyle("Admin/Resources/Assets/Css/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/simple-datatables.js",
                "Admin/Resources/Vendors/Simple-datatables/config-datatables.js")
            ->addVariableList(["newsList" => $newsList, 'tags' => $tags])
            ->view();
    }

    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/news")]
    public function editNews(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage.edit");

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
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage.edit");

        [$title, $desc, $content, $comm, $likes] = Utils::filterInput('title', 'desc', 'content', 'comm', 'likes');

        if (is_null($comm)) { $comm = 0;}
        if (is_null($likes)) { $likes = 0;}

        $slug = Utils::normalizeForSlug($title);

        $image = $_FILES['image'];

        if (empty($image)) {
            $image = NewsModel::getInstance()->getNewsById($id)->getImageName();
        }

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
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage.delete");

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

    #[NoReturn] #[Link("/tag/deleteSelected", Link::POST, [], "/cmw-admin/news", secure: false)]
    private function adminDeleteSelectedPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "news.manage");

        $selectedIds = $_POST['selectedIds'];

        if (empty($selectedIds)) {
            Flash::send(Alert::ERROR, "Contact", "Aucun message sélectionné");
            Redirect::redirectPreviousRoute();
        }

        $i = 0;
        foreach ($selectedIds as $selectedId) {
            $selectedId = FilterManager::filterData($selectedId, 11, FILTER_SANITIZE_NUMBER_INT);
            NewsTagsModel::getInstance()->deleteTag($selectedId);
            $i++;
        }
        Flash::send(Alert::SUCCESS, "News", "$i tags supprimé !");

        Redirect::redirectPreviousRoute();
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