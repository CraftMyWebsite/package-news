<?php

namespace CMW\Controller\News;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Uploads\ImagesException;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Manager\Xml\SitemapManager;
use CMW\Model\News\NewsModel;
use CMW\Model\News\NewsTagsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
use function is_null;
use const FILTER_SANITIZE_NUMBER_INT;
use const UPLOAD_ERR_OK;

/**
 * Class: @NewsController
 * @package News
 * @author Teyir
 * @version 0.0.1
 */
class NewsController extends AbstractController
{
    #[Link('/add', Link::GET, [], '/cmw-admin/news')]
    private function addNews(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage.add');

        $tags = NewsTagsModel::getInstance()->getTags();

        View::createAdminView('News', 'add')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->addVariableList(['tags' => $tags])
            ->view();
    }

    #[Link('/add', Link::POST, [], '/cmw-admin/news')]
    private function addNewsPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage.add');

        if (!isset($_FILES['image'])) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.errors.upload.image'));
            Redirect::redirectPreviousRoute();
        }

        $title = FilterManager::filterInputStringPost('title');
        $desc = FilterManager::filterInputStringPost('desc');
        $content = FilterManager::filterInputStringPost('content', null);
        $comm = isset($_POST['comm']);
        $likes = isset($_POST['likes']);
        $status = isset($_POST['status']);

        // We are storing $content for prevent error and not loose all data.
        $_SESSION['cmwNewsContent'] = $content;

        $slug = Utils::normalizeForSlug(FilterManager::filterInputStringPost('title'));
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $image = $_FILES['image'];

        try {
            // Upload image
            $imageName = ImagesManager::convertAndUpload($image, 'News');
        } catch (ImagesException $e) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.errors.upload.image'));
            Redirect::redirectPreviousRoute();
        }

        $news = NewsModel::getInstance()->createNews($title, $desc, $comm, $likes, $content, $slug, $userId, $imageName, $status);

        if (is_null($news)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('news.add.toasters.error'));
            Redirect::redirectPreviousRoute();
        }

        // Add tags
        if (isset($_POST['tags']) && $_POST['tags'] !== []) {
            foreach ($_POST['tags'] as $tag) {
                NewsTagsModel::getInstance()->addTagToNews($tag, $news->getNewsId());
            }
        }

        //Add news to sitemap if status is true
        if ($status) {
            SitemapManager::getInstance()->add($news->getFullUrl(), 0.7);
        }

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('news.add.toasters.success'));

        // If all good, we reset temp session data
        $_SESSION['cmwNewsContent'] = '';

        $this->clearNewsCache();

        Redirect::redirectPreviousRoute();
    }

    #[Link('/', Link::GET, [], '/cmw-admin/news')]
    private function listNews(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage');

        $newsList = NewsModel::getInstance()->getNews();
        $tags = NewsTagsModel::getInstance()->getTags();

        View::createAdminView('News', 'main')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->addVariableList(['newsList' => $newsList, 'tags' => $tags])
            ->view();
    }

    #[Link('/edit/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/news')]
    private function editNews(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage.edit');

        $news = NewsModel::getInstance()->getNewsById($id, true);

        if (is_null($news)) {
            Redirect::errorPage(404);
        }

        $tags = NewsTagsModel::getInstance()->getTags();

        View::createAdminView('News', 'edit')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->addVariableList(['news' => $news, 'tags' => $tags])
            ->view();
    }

    #[NoReturn] #[Link('/edit/:id', Link::POST, ['id' => '[0-9]+'], '/cmw-admin/news')]
    private function editNewsPost(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage.edit');

        $news = NewsModel::getInstance()->getNewsById($id, true);

        if (is_null($news)) {
            Redirect::errorPage(404);
        }

        $title = FilterManager::filterInputStringPost('title');
        $desc = FilterManager::filterInputStringPost('desc');
        $content = FilterManager::filterInputStringPost('content', null);
        $comm = isset($_POST['comm']);
        $likes = isset($_POST['likes']);
        $status = isset($_POST['status']);

        $slug = Utils::normalizeForSlug($title);

        $image = $_FILES['image'] ?? null;

        if (empty($image['name']) || !isset($image) || $image['error'] !== UPLOAD_ERR_OK) {
            $imageName = $news->getImageName();
        } else {
            ImagesManager::deleteImage($news->getImageName(), 'News/');
            $imageName = ImagesManager::convertAndUpload($image, 'News');
        }

        $updatedNews = NewsModel::getInstance()->updateNews($id, $title, $desc, $comm, $likes, $content, $slug, $imageName, $status);

        if (is_null($updatedNews)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Redirect::redirectPreviousRoute();
        }

        if (!$image) {
            Redirect::errorPage(404);
        }

        // Clear and add tags
        if (isset($_POST['tags']) && $_POST['tags'] !== []) {
            NewsTagsModel::getInstance()->clearTagsForANews($id);
            foreach ($_POST['tags'] as $tag) {
                NewsTagsModel::getInstance()->addTagToNews($tag, $id);
            }
        }

        //Update sitemap
        if (($news->isPublished() && $updatedNews->isPublished()) && $updatedNews->getSlug() === $news->getSlug()) {
            SitemapManager::getInstance()->update($news->getFullUrl(), 0.7);
        } else if ($updatedNews->isPublished() && $news->isPublished()) {
            SitemapManager::getInstance()->delete($news->getFullUrl());
            SitemapManager::getInstance()->add($updatedNews->getFullUrl(), 0.7);
        } else if ($updatedNews->isPublished() && !$news->isPublished()) {
            SitemapManager::getInstance()->add($updatedNews->getFullUrl(), 0.7);
        } else if (!$updatedNews->isPublished()) {
            SitemapManager::getInstance()->delete($news->getFullUrl());
        }

        $this->clearNewsCache();

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('news.edit.toasters.success'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/delete/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/news')]
    private function deleteNews(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage.delete');

        $news = NewsModel::getInstance()->getNewsById($id);

        if (is_null($news)) {
            Redirect::errorPage(404);
        }

        NewsModel::getInstance()->deleteNews($id);

        if ($news->isPublished()) {
            SitemapManager::getInstance()->delete($news->getFullUrl());
        }

        $this->clearNewsCache();

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('news.delete.toasters.success'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/tag', Link::POST, [], '/cmw-admin/news')]
    private function addNewsTagPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage');

        $name = FilterManager::filterInputStringPost('name');
        $icon = FilterManager::filterInputStringPost('icon', orElse: null);
        $color = FilterManager::filterInputStringPost('color', orElse: null);

        if (NewsTagsModel::getInstance()->createTag($name, $icon, $color)) {
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('news.tags.toasters.add.success'));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('news.tags.toasters.add.error'));
        }

        $this->clearNewsCache();

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/tag/delete/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/news')]
    private function deleteNewsTag(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage');

        if (NewsTagsModel::getInstance()->deleteTag($id)) {
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('news.tags.toasters.delete.success'));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('news.tags.toasters.delete.error'));
        }

        $this->clearNewsCache();

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/tag/deleteSelected', Link::POST, [], '/cmw-admin/news', secure: false)]
    private function adminDeleteSelectedPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage');

        $selectedIds = $_POST['selectedIds'];

        if (empty($selectedIds)) {
            Flash::send(Alert::ERROR, 'Contact', 'Aucun message sélectionné');
            Redirect::redirectPreviousRoute();
        }

        $i = 0;
        foreach ($selectedIds as $selectedId) {
            $selectedId = FilterManager::filterData($selectedId, 11, FILTER_SANITIZE_NUMBER_INT);
            NewsTagsModel::getInstance()->deleteTag($selectedId);
            $i++;
        }

        $this->clearNewsCache();

        Flash::send(Alert::SUCCESS, 'News', "$i tags supprimé !");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/tag/edit/:id', Link::POST, ['id' => '[0-9]+'], '/cmw-admin/news')]
    private function editNewsTagPost(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage');

        $name = FilterManager::filterInputStringPost('name');
        $icon = FilterManager::filterInputStringPost('icon', orElse: null);
        $color = FilterManager::filterInputStringPost('color', orElse: null);

        if (NewsTagsModel::getInstance()->editTag($id, $name, $icon, $color)) {
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('news.tags.toasters.edit.success'));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('news.tags.toasters.edit.error'));
        }

        $this->clearNewsCache();

        Redirect::redirectPreviousRoute();
    }

    /**
     * <p>Clear all news cache files (App/Storage/Cache/News)</p>
     * @return void
     */
    private function clearNewsCache(): void
    {
        $dir = EnvManager::getInstance()->getValue('DIR') . 'App/Storage/Cache/News/';
        SimpleCacheManager::deleteAllFiles($dir);
    }
}
