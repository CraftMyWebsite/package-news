<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("news.dashboard.title");
$description = LangManager::translate("news.dashboard.desc");
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-newspaper"></i> <span class="m-lg-auto"><?= LangManager::translate("news.dashboard.title") ?></span></h3>
</div>

<section>
    <div class="card">
        <div class="card-header">
            <h4><?= LangManager::translate("news.dashboard.title_add") ?></h4>
        </div>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("news.add.title") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" name="title" required placeholder="<?= LangManager::translate("news.add.title_placeholder") ?>" maxlength="255">
                                <div class="form-control-icon">
                                    <i class="fas fa-heading"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("news.add.desc") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" name="desc" required placeholder="<?= LangManager::translate("news.add.desc_placeholder") ?>" maxlength="255">
                                <div class="form-control-icon">
                                    <i class="fas fa-text-width"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("news.add.image") ?> :</h6>
                            <input required class="mt-2 form-control form-control-sm" type="file" id="image" name="image" accept=".png,.jpg,.jpeg,.webp,.svg,.gif">
                            <span><?= LangManager::translate("news.add.allow_files") ?></span>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" value="1" id="comm" name="comm" checked>
                                <label class="form-check-label" for="comm"><h6><?= LangManager::translate("news.add.enable_comm") ?></h6></label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" value="1" id="likes" name="likes" checked>
                                <label class="form-check-label" for="likes"><h6><?= LangManager::translate("news.add.enable_likes") ?></h6></label>
                            </div>
                        </div>
                </div>
                <h6><?= LangManager::translate("news.add.content") ?> :</h6>

                <textarea name="content" id="summernote-1"></textarea>

                <div class="text-center mt-2">
                    <button type="submit" class="btn btn-primary"><?= LangManager::translate("core.btn.add") ?></button>
                </div>
            </form>
        </div>
    </div>
</section>

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
                    <th class="text-center"><?= LangManager::translate("news.list.table.creation_date") ?></th>
                    <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
                </tr>
                </thead>
                <tbody class="text-center">
                    <?php /** @var \CMW\Entity\News\NewsEntity[] $newsList */ foreach ($newsList as $news) : ?>
                    <tr>
                        <td><?= $news->getTitle() ?></td>
                        <td><?= $news->getDescription() ?></td>
                        <td><?= $news->getAuthor()->getUsername() ?></td>
                        <td><?= $news->getDateCreated() ?></td>
                        <td>
                            <a href="../news/edit/<?= $news->getNewsId() ?>">
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