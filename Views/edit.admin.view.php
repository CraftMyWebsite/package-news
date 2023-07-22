<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Utils;
use CMW\Utils\Website;

$title = LangManager::translate("news.dashboard.title_edit");
$description = LangManager::translate("news.dashboard.desc");

/* @var \CMW\Entity\News\NewsEntity $news */
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-newspaper"></i> <span class="m-lg-auto"><?= LangManager::translate("news.dashboard.title") ?></span></h3>
</div>

<section>
    <div class="card">
        <div class="card-header">
            <h4><?= LangManager::translate("news.dashboard.title_edit") ?></h4>
        </div>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("news.add.title") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" name="title" id="title" required placeholder="<?= LangManager::translate("news.add.title_placeholder") ?>" maxlength="255" value="<?= $news->getTitle() ?>">
                                <div class="form-control-icon">
                                    <i class="fas fa-heading"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("news.add.desc") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" name="desc" id="desc" required placeholder="<?= LangManager::translate("news.add.desc_placeholder") ?>" maxlength="255" value="<?= $news->getDescription() ?>">
                                <div class="form-control-icon">
                                    <i class="fas fa-text-width"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("news.add.image") ?> :</h6>
                            <input class="mt-2 form-control form-control-sm" type="file" id="image" name="image" accept="png,jpg,jpeg,webp,svg,gif">
                            <span><?= LangManager::translate("news.add.allow_files") ?></span>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" value="1" id="comm" name="comm" <?= ($news->isCommentsStatus() ? "checked" : "") ?>>
                                <label class="form-check-label" for="comm"><h6><?= LangManager::translate("news.add.enable_comm") ?></h6></label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" value="1" id="likes" name="likes" <?= ($news->isLikesStatus() ? "checked" : "") ?>>
                                <label class="form-check-label" for="likes"><h6><?= LangManager::translate("news.add.enable_likes") ?></h6></label>
                            </div>
                        </div>
                </div>

                <h6><?= LangManager::translate("news.add.content") ?> :</h6>
            <textarea class="tinymce" name="content"><?= $news->getContentNotTranslate() ?></textarea>

                <div class="text-center mt-2">
                    <button type="submit" id="saveButton" class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
                </div>
            </form>
        </div>
    </div>
</section>