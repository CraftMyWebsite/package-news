<?php

namespace CMW\Implementation\News;

use CMW\Interface\Core\IMenus;

class NewsMenusImplementations implements IMenus {

    public function getRoutes(): array
    {
        return [
            'news'
        ];
    }

    public function getPackageName(): string
    {
        return 'News';
    }
}