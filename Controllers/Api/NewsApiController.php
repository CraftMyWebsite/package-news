<?php

namespace CMW\Controller\News\Api;

use CMW\Controller\OverApi\OverApi;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Mapper\News\Front\NewsFrontMapper;
use CMW\Model\News\NewsModel;
use CMW\Model\News\NewsTagsModel;
use CMW\Type\OverApi\RequestsErrorsTypes;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @NewsApiController
 * @package News
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class NewsApiController extends AbstractController
{
    #[NoReturn] #[Link("/news", Link::GET, scope: '/api')]
    private function getArticles(): void
    {
        $limit = isset($_GET['limit']) ? FilterManager::filterInputIntGet('limit', orElse: 9) : 9;
        $order = isset($_GET['order']) ? FilterManager::filterInputStringGet('order', maxLength: 5) : 'DESC';
        $tag = isset($_GET['tag']) ? FilterManager::filterInputIntGet('tag') : null;

        if ($order !== 'ASC' && $order !== 'DESC') {
            OverApi::returnError(RequestsErrorsTypes::WRONG_PARAMS, ['order' => 'Order must be ASC or DESC']);
        }

        if (!is_null($tag) && !is_numeric($tag)) {
            OverApi::returnError(RequestsErrorsTypes::WRONG_PARAMS, ['tag' => 'Tag must be a number']);
        }

        if (!is_null($tag)) {
            $news = NewsTagsModel::getInstance()->getNewsForTagById($tag, $limit, $order);
        } else {
            $news = NewsModel::getInstance()->getSomeNews($limit, $order);
        }

        OverApi::returnData(['news' => NewsFrontMapper::mapToFront($news)]);
    }
}
