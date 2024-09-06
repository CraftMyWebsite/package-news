<?php

namespace CMW\Implementation\News\Core;

use CMW\Interface\Core\IMenus;
use CMW\Model\News\NewsModel;

class NewsMenusImplementations implements IMenus
{
    public function getRoutes(): array
    {
        $slug = [];
        $slug['News'] = 'news';

        foreach (NewsModel::getInstance()->getNews() as $news) {
            $slug[$news->getTitle()] = 'news/' . $news->getSlug();
        }

        return $slug;
    }

    public function getPackageName(): string
    {
        return 'News';
    }
}
