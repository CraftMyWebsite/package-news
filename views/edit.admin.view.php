<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Utils;
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
           
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <input type="hidden" id="id" name="id" value="<?= $news->getNewsId() ?>">
                            <h6><?= LangManager::translate("news.add.title") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" id="title" required placeholder="<?= LangManager::translate("news.add.title_placeholder") ?>" maxlength="255" value="<?= $news->getTitle() ?>">
                                <div class="form-control-icon">
                                    <i class="fas fa-heading"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("news.add.desc") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" id="desc" required placeholder="<?= LangManager::translate("news.add.desc_placeholder") ?>" maxlength="255" value="<?= $news->getDescription() ?>">
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
                <div class="card-in-card" id="editorjs"></div>

                <div class="text-center mt-2">
                    <button type="submit" id="saveButton" class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
                </div>
        </div>
    </div>
</section>


<script>
    /**
     * Check inpt befor send
    */
     let input_title = document.querySelector("#title");
     let input_desc = document.querySelector("#desc");
     let button = document.querySelector("#saveButton");
     input_title.addEventListener("change", stateHandle);
     input_desc.addEventListener("change", stateHandle);
     function stateHandle() {
     if (document.querySelector("#title").value !="" && document.querySelector("#desc").value !="") {
      button.disabled = false;
      button.innerHTML = "<?= LangManager::translate("core.btn.save") ?>";
     }
     else {
      button.disabled = true;
      button.innerHTML = "<?= LangManager::translate("news.button.create_before") ?>";
     }
    }


    /**
     * EditorJS
     *  //TODO IMPLEMENT IMAGES
     */
    let editor = new EditorJS({
        placeholder: "<?= LangManager::translate("news.editor.start") ?>",
        logLevel: "ERROR",
        readOnly: false,
        holder: "editorjs",
        /**
         * Tools list
         */
        tools: {
            header: {
                class: Header,
                config: {
                    placeholder: "Entrez un titre",
                    levels: [2, 3, 4],
                    defaultLevel: 2
                }
            },
            image: {
                class: ImageTool,
                config: {
                    uploader: {
                        uploadByFile(file) {
                            let formData = new FormData();
                            formData.append('image', file);
                            return fetch("<?= Utils::getEnv()->getValue("PATH_SUBFOLDER")?>cmw-Admin/Pages/uploadImage/add", {
                                method: "POST",
                                body: formData
                            }).then(res => res.json())
                                .then(response => {
                                    return {
                                        success: 1,
                                        file: {
                                            url: "<?= Utils::getEnv()->getValue("PATH_URL")?>public/uploads/editor/" + response
                                        }
                                    }
                                })
                        }
                    }
                }
            },
            list: List,
            quote: {
                class: Quote,
                config: {
                    quotePlaceholder: "",
                    captionPlaceholder: "Auteur",
                },
            },
            warning: Warning,
            code: CodeTool,
            delimiter: Delimiter,
            table: Table,
            embed: {
                class: Embed,
                config: {
                    services: {
                        youtube: true,
                        coub: true
                    }
                }
            },
            Marker: Marker,
            underline: Underline,
        },
        defaultBlock: "paragraph",
        /**
         * Initial Editor data
         */
        data: <?= $news->getContentNotTranslate() ?>,
        onReady: function () {
            new Undo({editor});
            const undo = new Undo({editor});
            new DragDrop(editor);
        },
        onChange: function () {
        }
    });
    /**
     * Saving button
     */
    const saveButton = document.getElementById("saveButton");
    /**
     * Saving action
     */
    saveButton.addEventListener("click", function () {
        let comm_state = 0;
        if (document.getElementById("comm").checked) {
            comm_state = 1;
        }
        let likes_state = 0;
        if (document.getElementById("likes").checked) {
            likes_state = 1;
        }
        editor.save()
            .then((savedData) => {

                let formData = new FormData();
                formData.append('id', document.getElementById("id").value);
                formData.append('title', document.getElementById("title").value);
                formData.append('desc', document.getElementById("desc").value);
                formData.append('image', document.getElementById("image").files[0]);
                formData.append('content', JSON.stringify(savedData));
                formData.append('comm', comm_state.toString());
                formData.append('likes', likes_state.toString());

                fetch("<?= Utils::getEnv()->getValue("PATH_URL") ?>cmw-Admin/news/edit", {
                    method: "POST",
                    body: formData
                })

                button.disabled = true;
                button.innerHTML = "<?= LangManager::translate("news.button.saving") ?>";
                setTimeout(() => {
                            button.innerHTML = "<i style='color: #16C329;' class='fa-solid fa-check fa-shake'></i> Ok !";
                        }, 850);
                setTimeout(() => {
                            document.location.replace("<?= Utils::getHttpProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . 'cmw-Admin/news/manage'?>");
                        }, 1000);
                
            })
            .catch((error) => {
                alert("Error " + error);
            });
    });
</script>