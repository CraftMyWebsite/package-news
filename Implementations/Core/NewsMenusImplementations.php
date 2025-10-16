<?php

namespace CMW\Implementation\News\Core;

use CMW\Interface\Core\IMenus;
use CMW\Model\News\NewsModel;
use CMW\Model\News\NewsSettingsModel;

class NewsMenusImplementations implements IMenus
{
    public function getRoutes(): array
    {
        $slugPrefix = NewsSettingsModel::getNewsSlugPrefix();
        $slug = [];
        $slug['News'] = $slugPrefix;

        foreach (NewsModel::getInstance()->getNews() as $news) {
            $slug[$news->getTitle()] = $slugPrefix . '/' . $news->getSlug();
        }

        return $slug;
    }

    public function getPackageName(): string
    {
        return 'News';
    }
}
