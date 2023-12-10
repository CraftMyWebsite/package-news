<?php

namespace CMW\Package\News;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return "News";
    }

    public function version(): string
    {
        return "1.0.0";
    }

    public function authors(): array
    {
        return ["Teyir"];
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
                lang: "fr",
                icon: "fas fa-newspaper",
                title: "Actualités",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Gestion',
                        permission: 'news.manage',
                        url: 'news/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Ajouter',
                        permission: 'news.add',
                        url: 'news/add',
                    ),
                ]
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-newspaper",
                title: "News",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Manage',
                        permission: 'news.manage',
                        url: 'news/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Add',
                        permission: 'news.add',
                        url: 'news/add',
                    ),
                ]
            ),
        ];
    }
}