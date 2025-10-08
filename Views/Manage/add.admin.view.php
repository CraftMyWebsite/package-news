<?php

use CMW\Entity\News\NewsTagsEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var NewsTagsEntity[] $tags */

$title = LangManager::translate('news.dashboard.title');
$description = LangManager::translate('news.dashboard.desc');
?>

<div class="page-title">
    <h3><i class="fa-solid fa-newspaper"></i> <?= LangManager::translate('news.dashboard.title_add') ?></h3>
    <button form="addNews" type="submit" class="btn-primary"> <?= LangManager::translate('core.btn.save') ?></button>
</div>

<form id="addNews" action="" method="post" enctype="multipart/form-data">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>

    <div class="grid-3">
        <div class="col-span-2 card">
            <label for="content"><?= LangManager::translate('news.add.content') ?> :</label>
            <textarea class="tinymce" id="content" name="content"
                      data-tiny-height="600"><?= $_SESSION['cmwNewsContent'] ?? '' ?></textarea>
        </div>
        <div class="card space-y-4">
            <div>
                <label for="title"><?= LangManager::translate('news.add.title') ?> :</label>
                <div class="input-group">
                    <i class="fas fa-heading"></i>
                    <input type="text" name="title" id="title" required
                           placeholder="<?= LangManager::translate('news.add.title_placeholder') ?>"
                           maxlength="255">
                </div>
            </div>
            <div class="col-span-2">
                <label for="desc"><?= LangManager::translate('news.add.desc') ?> :</label>
                <div class="input-group">
                    <i class="fas fa-text-width"></i>
                    <input type="text" name="desc" id="desc" required
                           placeholder="<?= LangManager::translate('news.add.desc_placeholder') ?>"
                           maxlength="255">
                </div>
            </div>
            <div>
                <label><?= LangManager::translate('news.add.image') ?> :</label>
                <div class="drop-img-area" data-input-name="image"></div>
            </div>

            <div>
                <label for="tags"><?= LangManager::translate('news.tags.tags') ?> :</label>
                <select class="choices" id="tags" name="tags[]" multiple>
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?= $tag->getId() ?>"><?= $tag->getName() ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="mb-4">
                    <label class="toggle">
                        <p class="toggle-label"><?= LangManager::translate('news.add.enable_comm') ?></p>
                        <input class="toggle-input" name="comm" type="checkbox" id="comm" checked>
                        <div class="toggle-slider"></div>
                    </label>
                </div>
                <div class='mb-4'>
                    <label class='toggle'>
                        <p class='toggle-label'><?= LangManager::translate('news.add.enable_likes') ?></p>
                        <input name="likes" type="checkbox" value="1" id="likes" checked class="toggle-input">
                        <div class="toggle-slider"></div>
                    </label>
                </div>
                <div>
                    <label class='toggle'>
                        <p class='toggle-label'><?= LangManager::translate('news.add.status_toggle') ?></p>
                        <input name="status" type="checkbox" value="1" id="status" checked class="toggle-input">
                        <div class="toggle-slider"></div>
                    </label>
                </div>
                <div>
                    <label for="scheduled_date"><?= LangManager::translate('news.add.scheduled_date') ?>
                        <button data-tooltip-target="tooltip-top" type="button" data-tooltip-placement="top"><i
                                class="fas fa-circle-info"></i></button>
                        <div id="tooltip-top" role="tooltip" class="tooltip-content">
                            <?= LangManager::translate('news.add.scheduled_date_help') ?>
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                        :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-calendar-days"></i>
                        <input type="datetime-local" name="scheduled_date" id="scheduled_date"
                               placeholder="<?= LangManager::translate('news.add.scheduled_date_placeholder') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Handle Scheduled date and status -->
<!-- If scheduled date is set, disable status checkbox and uncheck it -->
<script>
    document.getElementById('scheduled_date').addEventListener('change', function () {
        const statusCheckbox = document.getElementById('status');
        if (this.value) {
            statusCheckbox.checked = false
            statusCheckbox.disabled = true
        } else {
            statusCheckbox.disabled = false
            statusCheckbox.checked = true
        }
    })
</script>