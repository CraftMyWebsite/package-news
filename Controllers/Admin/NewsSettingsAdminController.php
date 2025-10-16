<?php

namespace CMW\Controller\News\Admin;

use CMW\Controller\Users\UsersController;
use CMW\Entity\News\NewsSettingsEntity;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Manager\Xml\SitemapManager;
use CMW\Model\News\NewsModel;
use CMW\Model\News\NewsSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @NewsSettingsAdminController
 * @package News
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class NewsSettingsAdminController extends AbstractController
{
    public array $allowedPrefixSlug = ['news', 'blog', 'actu', 'articles', 'actualites'];
    public string $defaultPrefixSlug = 'news';

    #[Link('/settings', Link::GET, [], '/cmw-admin/news')]
    private function listNews(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage');

        $settings = NewsSettingsModel::getInstance()->getSettings();

        //Generate cron token if not exists
        $this->initCronToken($settings);

        View::createAdminView('News', 'settings')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->addVariableList(['settings' => $settings])
            ->view();
    }

    #[NoReturn] #[Link('/settings', Link::POST, [], '/cmw-admin/news')]
    private function saveSettings(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'news.manage');

        $enableScheduledPublishing = FilterManager::filterInputIntPost('scheduled_publications', 1, 0);
        $slugPrefix = filter_input(INPUT_POST, 'slug_prefix', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'news';

        if (!\in_array($slugPrefix, $this->allowedPrefixSlug, true)) {
            $slugPrefix = $this->defaultPrefixSlug;
        }

        $storedSettings = NewsSettingsModel::getInstance()->getSettings();
        $oldSlugPrefix = $storedSettings?->getSlugPrefix() ?? $this->defaultPrefixSlug;

        if ($storedSettings === null) {
            $settings = new NewsSettingsEntity($enableScheduledPublishing, $this->generateCronToken(), $slugPrefix);
        } else {
            $settings = new NewsSettingsEntity($enableScheduledPublishing, $storedSettings->getCronKey(), $slugPrefix);
        }

        if (NewsSettingsModel::getInstance()->setSettings($settings)) {
            // Update sitemap URLs if slug prefix changed
            if ($oldSlugPrefix !== $slugPrefix) {
                $this->updateSitemapUrls($oldSlugPrefix, $slugPrefix);
            }

            Flash::send(
                Alert::SUCCESS,
                LangManager::translate("core.toaster.success"),
                LangManager::translate("news.settings.toasters.save.success")
            );
        } else {
            Flash::send(
                Alert::ERROR,
                LangManager::translate("core.toaster.error"),
                LangManager::translate("news.settings.toasters.save.error")
            );
        }

        Redirect::redirectPreviousRoute();
    }

    /**
     * <p>Generate a CRON Token if necessary</p>
     * @param NewsSettingsEntity|null $settings
     * @return void
     */
    private function initCronToken(?NewsSettingsEntity $settings): void
    {
        //Set a cron token if not exists
        if ($settings === null) {
            $token = $this->generateCronToken();
            if (!NewsSettingsModel::getInstance()->setCronToken($token)) {
                Flash::send(
                    Alert::ERROR,
                    LangManager::translate("core.toaster.error"),
                    LangManager::translate("news.settings.toasters.cron_token.error")
                );
            }
        }
    }

    /**
     * <p>Generate a random UUID</p>
     * @return string
     */
    private function generateCronToken(): string
    {
        return Utils::generateUUID();
    }

    /**
     * <p>Update all news URLs in sitemap when slug prefix changes</p>
     * @param string $oldSlugPrefix
     * @param string $newSlugPrefix
     * @return void
     */
    private function updateSitemapUrls(string $oldSlugPrefix, string $newSlugPrefix): void
    {
        $newsList = NewsModel::getInstance()->getNews(true);

        if (empty($newsList)) {
            return;
        }

        $sitemapManager = SitemapManager::getInstance();

        foreach ($newsList as $news) {
            $oldUrl = $oldSlugPrefix . '/' . $news->getSlug();
            $sitemapManager->delete($oldUrl);

            $newUrl = $newSlugPrefix . '/' . $news->getSlug();
            $sitemapManager->add($newUrl, 0.7);
        }
    }
}
