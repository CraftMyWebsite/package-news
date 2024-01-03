<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\News\NewsTagsEntity $tag */

$title = LangManager::translate("news.dashboard.title");
$description = LangManager::translate("news.dashboard.desc");
?>


<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>
                    <?= LangManager::translate("news.tags.edit.title") ?> <b><?= $tag->getName() ?></b>
                </h4>
            </div>
            <div class="card-body">
                <form method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="form-group mandatory has-icon-left">
                        <label class="form-label" for="name"><?= LangManager::translate("news.tags.name") ?></label>
                        <div class="position-relative">
                            <input type="text" class="form-control" name="name" id="name" autocomplete="off"
                                   placeholder="Devblog" value="<?= $tag->getName() ?>" required>
                            <div class="form-control-icon">
                                <i class="fa-solid fa-tag"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group has-icon-left">
                        <label class="form-label" for="icon"><?= LangManager::translate("news.tags.icon") ?> <small>(FontAwesome)</small></label>
                        <div class="position-relative">
                            <input type="text" class="form-control" name="icon" id="icon" autocomplete="off"
                                   value="<?= $tag->getIcon() ?>" placeholder="fa-solid fa-tag">
                            <div class="form-control-icon">
                                <i class="fa-solid fa-tag"></i>
                            </div>
                        </div>
                    </div>

                    <label class="form-label" for="color"><?= LangManager::translate("news.tags.color") ?>
                        <input type="color" class="form-control form-control-color" name="color"
                               value="<?= $tag->getColor() ?>">
                    </label>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <?= LangManager::translate("core.btn.add") ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>