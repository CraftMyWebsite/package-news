<?php

use CMW\Utils\SecurityService;

$title = NEWS_DASHBOARD_TITLE_EDIT;
$description = NEWS_DASHBOARD_DESC;

/* @var \CMW\Entity\News\NewsEntity $news */
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="" method="post" enctype="multipart/form-data">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="card card-primary">

                        <div class="card-header">
                            <h3 class="card-title"><?= NEWS_DASHBOARD_TITLE_EDIT ?> :</h3>
                        </div>

                        <div class="card-body">

                            <label for="title"><?= NEWS_ADD_TITLE ?></label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                </div>
                                <input type="text" name="title" class="form-control" value="<?= $news->getTitle() ?>"
                                       placeholder="<?= NEWS_ADD_TITLE_PLACEHOLDER ?>" maxlength="255" required>
                            </div>

                            <label for="desc"><?= NEWS_ADD_DESC ?></label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-text-width"></i></span>
                                </div>
                                <input type="text" name="desc" class="form-control" value="<?= $news->getDescription() ?>"
                                       placeholder="<?= NEWS_ADD_DESC_PLACEHOLDER ?>" maxlength="255" required>
                            </div>

                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success input-group mb-3">
                                <input type="checkbox" name="comm" value="true" class="custom-control-input" <?= ($news->isCommentsStatus() ? "checked" : "") ?>
                                       id="comm" checked required>
                                <label class="custom-control-label"
                                       for="comm"><?= NEWS_ADD_ENABLE_COMM ?></label>
                            </div>

                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success input-group mb-3">
                                <input type="checkbox" name="likes" value="true" class="custom-control-input" <?= ($news->isLikesStatus() ? "checked" : "") ?>
                                       id="likes" checked required>
                                <label class="custom-control-label"
                                       for="likes"><?= NEWS_ADD_ENABLE_LIKES ?></label>
                            </div>


                            <div class="form-group">

                                <span><?= NEWS_ADD_IMAGE ?></span>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image" accept=".png, .jpg, .jpeg, .webp, .gif">
                                    <label class="custom-file-label" for="image"><?= NEWS_ADD_SELECT_IMAGE ?></label>
                                </div>
                            </div>



                            <label for="content" class="mt-3"><?= NEWS_ADD_CONTENT ?></label>
                            <div class="input-group mb-3">
                                <textarea id="summernote" name="content" class="form-control" required>
                                <?= $news->getContent() ?>
                                </textarea>

                            </div>

                        </div>


                        <div class="card-footer">
                            <button type="submit"
                                    class="btn btn-primary float-right"><?= CORE_BTN_SAVE ?></button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>