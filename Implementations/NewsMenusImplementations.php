<?php

namespace CMW\Implementation\News;

use CMW\Interface\Core\IMenus;
use CMW\Manager\Lang\LangManager;
use CMW\Model\News\NewsModel;


class NewsMenusImplementations implements IMenus {

    public function getRoutes(): array
    {
        $slug = [];
        $slug['News'] = 'news';

        foreach ((new NewsModel())->getNews() as $news) {
            $slug[$news->getTitle()] = "p/" . $news->getSlug();
        }

        return $slug;
    }

    public function getPackageName(): string
    {
        return 'News';
    }
}