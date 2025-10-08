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

        $storedSettings = NewsSettingsModel::getInstance()->getSettings();

        if ($storedSettings === null) {
            $settings = new NewsSettingsEntity($enableScheduledPublishing, $this->generateCronToken());
        } else {
            $settings = new NewsSettingsEntity($enableScheduledPublishing, $storedSettings->getCronKey());
        }

        if (NewsSettingsModel::getInstance()->setSettings($settings)) {
            Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"), "Paramètres enregistrés avec succès.");
        } else {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"), "Une erreur est survenue lors de l'enregistrement des paramètres.");
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
                Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"), "Une erreur est survenue lors de la génération du token cron.");
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
}
