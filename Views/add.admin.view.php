<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\News\NewsTagsEntity[] $tags */

$title = LangManager::translate("news.dashboard.title");
$description = LangManager::translate("news.dashboard.desc");
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-newspaper"></i> <span
            class="m-lg-auto"><?= LangManager::translate("news.dashboard.title") ?></span></h3>
</div>

<section>
    <div class="card">
        <div class="card-header">
            <h4><?= LangManager::translate("news.dashboard.title_add") ?></h4>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <h6><?= LangManager::translate("news.add.title") ?> :</h6>
                        <div class="form-group position-relative has-icon-left">
                            <input name="title" type="text" class="form-control" id="title" required
                                   placeholder="<?= LangManager::translate("news.add.title_placeholder") ?>"
                                   maxlength="255">
                            <div class="form-control-icon">
                                <i class="fas fa-heading"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h6><?= LangManager::translate("news.add.desc") ?> :</h6>
                        <div class="form-group position-relative has-icon-left">
                            <input type="text" class="form-control" name="desc" id="desc" required
                                   placeholder="<?= LangManager::translate("news.add.desc_placeholder") ?>"
                                   maxlength="255">
                            <div class="form-control-icon">
                                <i class="fas fa-text-width"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h6><?= LangManager::translate("news.add.image") ?> :</h6>
                        <input name="image" required class="mt-2 form-control form-control-sm" type="file" id="image"
                               accept=".png,.jpg,.jpeg,.webp,.svg,.gif">
                        <span><?= LangManager::translate("news.add.allow_files") ?></span>

                        <h6 class="mt-1"><?= LangManager::translate("news.tags.tags") ?> :</h6>
                        <fieldset class="form-group">
                            <select class="choices choices__list--multiple" name="tags[]" multiple>
                                <?php foreach ($tags as $tag) : ?>
                                    <option value="<?= $tag->getId() ?>"><?= $tag->getName() ?></option>
                                <?php endforeach; ?>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" name="comm" type="checkbox" value="1" id="comm" checked>
                            <label class="form-check-label" for="comm">
                                <h6><?= LangManager::translate("news.add.enable_comm") ?></h6></label>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" name="likes" type="checkbox" value="1" id="likes" checked>
                            <label class="form-check-label" for="likes">
                                <h6><?= LangManager::translate("news.add.enable_likes") ?></h6></label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6><?= LangManager::translate("news.add.content") ?> :</h6>
                    <textarea class="tinymce" name="content"><?= $_SESSION['cmwNewsContent'] ?? '' ?></textarea>
                </div>
                <div class="text-center mt-2">
                    <button id="saveButton" type="submit" class="btn btn-primary">
                        <?= LangManager::translate("core.btn.save") ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>