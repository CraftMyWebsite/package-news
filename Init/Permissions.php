<?php

namespace CMW\Permissions\News;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Permission\IPermissionInit;
use CMW\Manager\Permission\PermissionInitType;

class Permissions implements IPermissionInit
{
    public function permissions(): array
    {
        return [
            new PermissionInitType(
                code: 'news.add',
                description: LangManager::translate('news.permissions.news.add'),
            ),
            new PermissionInitType(
                code: 'news.manage',
                description: LangManager::translate('news.permissions.news.manage'),
            ),
            new PermissionInitType(
                code: 'news.edit',
                description: LangManager::translate('news.permissions.news.edit'),
            ),
            new PermissionInitType(
                code: 'news.delete',
                description: LangManager::translate('news.permissions.news.delete'),
            ),
        ];
    }

}