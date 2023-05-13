<?php

namespace CMW\Implementation\News;

use CMW\Interface\Core\IMenus;
use CMW\Manager\Lang\LangManager;

class NewsMenusImplementations implements IMenus {

    public function getRoutes(): array
    {
        return [
            LangManager::translate('news.news') => 'news'
        ];
    }

    public function getPackageName(): string
    {
        return 'News';
    }
}