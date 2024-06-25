<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\News\NewsTagsModel;
use CMW\Utils\Website;

/* @var \CMW\Entity\News\NewsTagsEntity[] $tags */
/** @var \CMW\Entity\News\NewsEntity[] $newsList */

$title = LangManager::translate("news.dashboard.title");
$description = LangManager::translate("news.dashboard.desc");
?>

<h3><i class="fa-solid fa-newspaper"></i> <?= LangManager::translate("news.dashboard.title") ?></h3>

<div class="card">
    <div class="lg:flex justify-between">
        <h6><?= LangManager::translate("news.list.list") ?></h6>
        <a href="add" class="btn-primary" type="button"><?= LangManager::translate("core.btn.add") ?></a>
    </div>
    <div class="table-container">
        <table id="table2" data-load-per-page="10" >
            <thead>
            <tr>
                <th><?= LangManager::translate("news.list.table.title") ?></th>
                <th><?= LangManager::translate("news.list.table.description") ?></th>
                <th><?= LangManager::translate("news.list.table.author") ?></th>
                <th><?= LangManager::translate("news.list.table.link") ?></th>
                <th><?= LangManager::translate("news.list.table.views") ?></th>
                <th><?= LangManager::translate("news.list.table.creation_date") ?></th>
                <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($newsList as $news) : ?>
                <tr>
                    <td><?= mb_strimwidth($news->getTitle(), 0, 20, '...') ?></td>
                    <td><?= mb_strimwidth($news->getDescription(), 0, 20, '...') ?></td>
                    <td><?= $news->getAuthor()->getPseudo() ?></td>
                    <td>
                        <a target="_blank" class="link"
                           href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "news/" . $news->getSlug() ?>">
                            <?= mb_strimwidth(Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "news/" . $news->getSlug(), 0, 35, '...') ?>
                        </a>
                    </td>
                    <td><?= $news->getViews() ?></td>
                    <td><?= $news->getDateCreated() ?></td>
                    <td class="text-center space-x-2">
                        <a class="me-3" href="../news/edit/<?= $news->getNewsId() ?>">
                            <i class="text-info fa-solid fa-gears"></i>
                        </a>
                        <button data-modal-toggle="modal-delete-news-<?= $news->getNewsId() ?>" type="button"><i class="text-danger fas fa-trash-alt"></i></button>
                    </td>
                </tr>
                <div id="modal-delete-news-<?= $news->getNewsId() ?>" class="modal-container">
                    <div class="modal">
                        <div class="modal-header-danger">
                            <h6><?= LangManager::translate("news.modal.delete") ?> <?= $news->getTitle() ?></h6>
                            <button type="button" data-modal-hide="modal-delete-news-<?= $news->getNewsId() ?>"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body">
                            <?= LangManager::translate("news.modal.deletealert") ?>
                        </div>
                        <div class="modal-footer">
                            <a href="../news/delete/<?= $news->getNewsId() ?>" class="btn-danger">
                                <?= LangManager::translate("core.btn.delete") ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid-2 mt-4">
    <div class="card">
        <div class="lg:flex justify-between">
            <h6><?= LangManager::translate("news.tags.list.title") ?></h6>
            <div class="space-x-2">
                <button type="submit" class="btn-danger btn-mass-delete loading-btn" data-loading-btn="Chargement" data-target-table="1">
                    Supprimer la selection
                </button>
                <button data-modal-toggle="modal-tag-add" class="btn-primary" type="button"><?= LangManager::translate("core.btn.add") ?></button>
            </div>

        </div>
        <div class="table-container">
            <table class="table-checkeable" data-form-action="tag/deleteSelected" id="table1">
                <thead>
                <tr>
                    <th class="mass-selector"></th>
                    <th><?= LangManager::translate("news.tags.name") ?></th>
                    <th><?= LangManager::translate("news.tags.icon") ?></th>
                    <th><?= LangManager::translate("news.tags.list.associatedNews") ?></th>
                    <th class="text-center"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($tags as $tag) : ?>
                    <tr>
                        <td class="item-selector" data-value="<?= $tag->getId() ?>"></td>
                        <td><?= $tag->getName() ?></td>
                        <td>
                            <i class="<?= $tag->getIcon() ?>" style="color: <?= $tag->getColor() ?>"></i>
                        </td>
                        <td><?= NewsTagsModel::getInstance()->getNewsNumberForTag($tag->getId()) ?></td>
                        <td class="space-x-2 text-center">
                            <button data-modal-toggle="modal-edit-<?= $tag->getId() ?>" type="button"><i class="text-info fa-solid fa-gears"></i></button>
                            <button data-modal-toggle="modal-delete-<?= $tag->getId() ?>" type="button"><i class="text-danger fas fa-trash-alt"></i></button>

                            <div id="modal-edit-<?= $tag->getId() ?>" class="modal-container">
                                <div class="modal">
                                    <div class="modal-header">
                                        <h6><?= LangManager::translate("news.tags.edit.title") ?> <?= $tag->getName() ?></h6>
                                        <button type="button" data-modal-hide="modal-edit-<?= $tag->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <form method="post" action="tag/edit/<?= $tag->getId() ?>">
                                        <?php (new SecurityManager())->insertHiddenToken() ?>
                                        <div class="modal-body">
                                            <div class="grid-2">
                                                <div>
                                                    <label for="name"><?= LangManager::translate("news.tags.name") ?> :</label>
                                                    <div class="input-group">
                                                        <i class="fa-solid fa-tag"></i>
                                                        <input type="text" name="name" id="name" autocomplete="off"
                                                               placeholder="Devblog" value="<?= $tag->getName() ?>" required>
                                                    </div>
                                                </div>
                                                <div class="icon-picker" data-id="icon" data-name="icon" data-label="<?= LangManager::translate("news.tags.icon") ?>" data-placeholder="Sélectionner un icon" data-value="<?= $tag->getIcon() ?>"></div>
                                            </div>
                                            <label class="form-label" for="color"><?= LangManager::translate("news.tags.color") ?>
                                                <input type="color" class="form-control form-control-color" name="color" value="<?= $tag->getColor() ?>">
                                            </label>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn-primary"><?= LangManager::translate("core.btn.edit") ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div id="modal-delete-<?= $tag->getId() ?>" class="modal-container">
                                <div class="modal">
                                    <div class="modal-header-danger">
                                        <h6><?= LangManager::translate("news.modal.delete") ?> <?= $tag->getName() ?></h6>
                                        <button type="button" data-modal-hide="modal-delete-<?= $tag->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        <?= LangManager::translate("news.modal.deletealert") ?>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="tag/delete/<?= $tag->getId() ?>" class="btn-danger">
                                            <?= LangManager::translate("core.btn.delete") ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-tag-add" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6><?= LangManager::translate("news.tags.add.title") ?></h6>
            <button type="button" data-modal-hide="modal-tag-add"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post" action="tag">
            <?php (new SecurityManager())->insertHiddenToken() ?>
        <div class="modal-body">
            <div class="grid-2">
                <div>
                    <label for="name"><?= LangManager::translate("news.tags.name") ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-tag"></i>
                        <input type="text" name="name" id="name" autocomplete="off"
                               placeholder="Devblog" required>
                    </div>
                </div>
                <div class="icon-picker" data-id="icon" data-name="icon" data-label="<?= LangManager::translate("news.tags.icon") ?>" data-placeholder="Sélectionner un icon" data-value=""></div>
            </div>
            <label class="form-label" for="color"><?= LangManager::translate("news.tags.color") ?>
                <input type="color" class="form-control form-control-color" name="color">
            </label>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn-primary">
                <?= LangManager::translate("core.btn.add") ?>
            </button>
        </div>
        </form>
    </div>
</div>