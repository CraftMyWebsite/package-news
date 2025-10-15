<?php

namespace CMW\Package\News;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\IPackageConfigV2;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfigV2
{
    public function name(): string
    {
        return 'News';
    }

    public function version(): string
    {
        return '1.6.0';
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
                icon: 'fas fa-newspaper',
                title: LangManager::translate('news.menu.title'),
                url: null,
                permission: null,
                subMenus: [
                  new PackageSubMenuType(
                      title: LangManager::translate('news.menu.settings'),
                      permission: 'news.manage',
                      url: 'news/settings',
                  ) ,
                  new PackageSubMenuType(
                        title: LangManager::translate('news.menu.news'),
                        permission: 'news.manage',
                        url: 'news/manage',
                  )
                ],
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

    public function cmwVersion(): string
    {
        return "beta-01";
    }

    public function imageLink(): ?string
    {
        return null;
    }

    public function compatiblesPackages(): array
    {
        return ['OverApi'];
    }
}
