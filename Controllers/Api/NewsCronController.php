<?php

namespace CMW\Controller\News\Api;

use CMW\Controller\News\Admin\NewsManageAdminController;
use CMW\Controller\OverApi\OverApi;
use CMW\Entity\News\Dto\NewsScheduledToPublishEntity;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Xml\SitemapManager;
use CMW\Model\News\NewsScheduledModel;
use CMW\Model\News\NewsSettingsModel;
use CMW\Type\OverApi\RequestsErrorsTypes;
use JetBrains\PhpStorm\NoReturn;
use function array_map;

/**
 * Class: @NewsCronController
 * @package News
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class NewsCronController extends AbstractController
{
    #[NoReturn] #[Link("/scheduled/publishing", Link::GET, scope: '/news/cron')]
    private function cronScheduledPublishing(): void
    {
        $settings = NewsSettingsModel::getInstance()->getSettings();

        if ($settings === null || !$settings->isEnableScheduledPublishing()) {
            OverApi::returnError(RequestsErrorsTypes::NOT_FOUND);
        }

        $key = FilterManager::filterInputStringGet("key");

        if ($key !== $settings->getCronKey()) {
            OverApi::returnError(RequestsErrorsTypes::NON_AUTHORIZED_REQUEST);
        }

        $currentTimestamp = (new \DateTime())->format('Y-m-d H:i:s');

        $newsToPublish = NewsScheduledModel::getInstance()->getNewsToPublished($currentTimestamp);

        if (empty($newsToPublish)) {
            OverApi::returnData(['status' => true, 'message' => 'No news to publish']);
        }

        OverApi::returnData(['status' => $this->publishNews($newsToPublish), "message" => "News published", "count" => \count($newsToPublish)]);
    }

    /**
     * @param NewsScheduledToPublishEntity[] $news
     * @return bool
     */
    private function publishNews(array $news): bool
    {
        $newsId = array_map(static fn($article) => $article->getNewsId(), $news);

        if (!NewsScheduledModel::getInstance()->updateNewsStatus($newsId)) {
            return false;
        }

        //Sitemap
        foreach ($news as $article) {
            SitemapManager::getInstance()->delete($article->getFullUrl());
            SitemapManager::getInstance()->add($article->getFullUrl(), 0.7);
        }

        NewsManageAdminController::getInstance()->clearNewsCache();

        return true;
    }
}
