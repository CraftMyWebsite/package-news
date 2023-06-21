<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Utils;
use CMW\Utils\Website;

$title = LangManager::translate("news.dashboard.title");
$description = LangManager::translate("news.dashboard.desc");
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-newspaper"></i> <span class="m-lg-auto"><?= LangManager::translate("news.dashboard.title") ?></span></h3>
</div>

<section>
    <div class="card">
        <div class="card-header">
            <h4><?= LangManager::translate("news.list.list") ?></h4>
        </div>
        <div class="card-body">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th class="text-center"><?= LangManager::translate("news.list.table.title") ?></th>
                    <th class="text-center"><?= LangManager::translate("news.list.table.description") ?></th>
                    <th class="text-center"><?= LangManager::translate("news.list.table.author") ?></th>
                    <th class="text-center"><?= LangManager::translate("news.list.table.link") ?></th>
                    <th class="text-center"><?= LangManager::translate("news.list.table.views") ?></th>
                    <th class="text-center"><?= LangManager::translate("news.list.table.creation_date") ?></th>
                    <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
                </tr>
                </thead>
                <tbody class="text-center">
                    <?php /** @var \CMW\Entity\News\NewsEntity[] $newsList */ foreach ($newsList as $news) : ?>
                    <tr>
                        <td><?= $news->getTitle() ?></td>
                        <td><?= $news->getDescription() ?></td>
                        <td><?= $news->getAuthor()->getPseudo() ?></td>
                        <td>
                            <a target="_blank" href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . "news/" .$news->getSlug() ?>">
                                <?= mb_strimwidth(Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . "news/" .$news->getSlug(), 0, 45, '...') ?>
                            </a>   
                        </td>
                        <td><?= $news->getViews() ?></td>
                        <td><?= $news->getDateCreated() ?></td>
                        <td>
                            <a class="me-3" href="../news/edit/<?= $news->getNewsId() ?>">
                                <i class="text-primary fa-solid fa-gears"></i>
                            </a>
                            <a type="button" data-bs-toggle="modal" data-bs-target="#delete-<?= $news->getNewsId() ?>">
                                <i class="text-danger fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <div class="modal fade text-left" id="delete-<?= $news->getNewsId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title white" id="myModalLabel160"><?= LangManager::translate("news.modal.delete") ?> <?= $news->getTitle() ?></h5>
                                </div>
                                <div class="modal-body">
                                    <?= LangManager::translate("news.modal.deletealert") ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block"><?= LangManager::translate("core.btn.close") ?></span>
                                    </button>
                                    <a href="../news/delete/<?= $news->getNewsId() ?>" class="btn btn-danger ml-1">
                                        <i class="bx bx-check d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block"><?= LangManager::translate("core.btn.delete") ?></span>
                                    </a>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>