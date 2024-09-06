<?php

namespace CMW\Package\News;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return 'News';
    }

    public function version(): string
    {
        return '0.0.1';
    }

    public function authors(): array
    {
        return ['Teyir'];
    }

    public function isGame(): bool
    {
        return false;
    }

    public function isCore(): bool
    {
        return false;
    }

    public function menus(): ?array
    {
        return [
            new PackageMenuType(
                lang: 'fr',
                icon: 'fas fa-newspaper',
                title: 'Actualités',
                url: 'news',
                permission: 'news.manage',
                subMenus: []
            ),
            new PackageMenuType(
                lang: 'en',
                icon: 'fas fa-newspaper',
                title: 'News',
                url: 'news',
                permission: 'news.manage',
                subMenus: []
            ),
        ];
    }

    public function requiredPackages(): array
    {
        return ['Core'];
    }

    public function uninstall(): bool
    {
        // Return true, we don't need other operations for uninstall.
        return true;
    }
}
