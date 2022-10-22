<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\SecurityService;

$title = LangManager::translate("news.dashboard.title_add");
$description = LangManager::translate("news.dashboard.desc");
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="" method="post" enctype="multipart/form-data">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="card card-primary">

                        <div class="card-header">
                            <h3 class="card-title"><?= LangManager::translate("news.dashboard.title_add") ?> :</h3>
                        </div>

                        <div class="card-body">

                            <label for="title"><?= LangManager::translate("news.add.title") ?></label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                </div>
                                <input type="text" name="title" class="form-control"
                                       placeholder="<?= LangManager::translate("news.add.title_placeholder") ?>"
                                       maxlength="255" required>
                            </div>

                            <label for="desc"><?= LangManager::translate("news.add.desc") ?></label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-text-width"></i></span>
                                </div>
                                <input type="text" name="desc" class="form-control"
                                       placeholder="<?= LangManager::translate("news.add.desc_placeholder") ?>"
                                       maxlength="255" required>
                            </div>

                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success input-group mb-3">
                                <input type="checkbox" name="comm" value="true" class="custom-control-input"
                                       id="comm" checked>
                                <label class="custom-control-label"
                                       for="comm"><?= LangManager::translate("news.add.enable_comm") ?></label>
                            </div>

                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success input-group mb-3">
                                <input type="checkbox" name="likes" value="true" class="custom-control-input"
                                       id="likes" checked>
                                <label class="custom-control-label"
                                       for="likes"><?= LangManager::translate("news.add.enable_likes") ?></label>
                            </div>


                            <div class="form-group">

                                <span><?= LangManager::translate("news.add.image") ?></span>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image"
                                           accept=".png, .jpg, .jpeg, .webp, .gif" required>
                                    <label class="custom-file-label"
                                           for="image"><?= LangManager::translate("news.add.select_image") ?></label>
                                </div>
                            </div>


                            <label for="content"
                                   class="mt-3"><?= LangManager::translate("news.add.content") ?></label>
                            <div class="input-group mb-3">
                                <textarea id="summernote" name="content" class="form-control" required> </textarea>

                            </div>

                        </div>


                        <div class="card-footer">
                            <button type="submit"
                                    class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save") ?></button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>