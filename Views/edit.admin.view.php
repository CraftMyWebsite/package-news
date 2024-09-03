<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("news.dashboard.title_edit");
$description = LangManager::translate("news.dashboard.desc");

/* @var \CMW\Entity\News\NewsEntity $news */
/* @var \CMW\Entity\News\NewsTagsEntity[] $tags */
?>

<div class="page-title">
    <h3><i class="fa-solid fa-newspaper"></i> <?= LangManager::translate("news.dashboard.title") . ": " . $news->getTitle() ?></h3>
    <button form="addNews" type="submit" class="btn-primary"> <?= LangManager::translate("core.btn.save") ?></button>
</div>

<form id="addNews" action="" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>

    <div class="grid-3">
        <div class="col-span-2 card">
            <label for="content"><?= LangManager::translate("news.add.content") ?> :</label>
            <textarea class="tinymce" id="content" name="content" data-tiny-height="600"><?= $news->getContentNotTranslate() ?></textarea>
        </div>
        <div class="card space-y-4">
            <div>
                <label for="title"><?= LangManager::translate("news.add.title") ?> :</label>
                <div class="input-group">
                    <i class="fas fa-heading"></i>
                    <input type="text" name="title" id="title" value="<?= $news->getTitle() ?>" required
                           placeholder="<?= LangManager::translate("news.add.title_placeholder") ?>"
                           maxlength="255">
                </div>
            </div>
            <div class="col-span-2">
                <label for="desc"><?= LangManager::translate("news.add.desc") ?> :</label>
                <div class="input-group">
                    <i class="fas fa-text-width"></i>
                    <input type="text" name="desc" id="desc" required value="<?= $news->getDescription() ?>"
                           placeholder="<?= LangManager::translate("news.add.desc_placeholder") ?>"
                           maxlength="255">
                </div>
            </div>
            <label><?= LangManager::translate("news.add.image") ?> :</label>
            <div class="grid-2">
                <div>
                    <img class="rounded-lg mx-auto" width="70%" src="<?= $news->getFullImageLink() ?>">
                </div>
                <div class="drop-img-area" data-input-name="image"></div>
            </div>

            <div>
                <label for="tags"><?= LangManager::translate("news.tags.tags") ?> :</label>
                <select class="choices" id="tags" name="tags[]" multiple>
                    <?php foreach ($tags as $tag) : ?>
                        <option value="<?= $tag->getId() ?>"
                            <?= $news->hasTag($tag->getId()) ? 'selected' : '' ?>>
                            <?= $tag->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="mb-4">
                    <label class="toggle">
                        <p class="toggle-label"><?= LangManager::translate("news.add.enable_comm") ?></p>
                        <input class="toggle-input" name="comm" type="checkbox" id="comm" <?= ($news->isCommentsStatus() ? "checked" : "") ?> >
                        <div class="toggle-slider"></div>
                    </label>
                </div>
                <div>
                    <label class="toggle">
                        <p class="toggle-label"><?= LangManager::translate("news.add.enable_likes") ?></p>
                        <input name="likes" type="checkbox" value="1" id="likes" <?= ($news->isLikesStatus() ? "checked" : "") ?> class="toggle-input">
                        <div class="toggle-slider"></div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>