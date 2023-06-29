<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Utils;
use CMW\Utils\Response;
use CMW\Utils\Website;

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
        <div class="card-body">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <h6><?= LangManager::translate("news.add.title") ?> :</h6>
                        <div class="form-group position-relative has-icon-left">
                            <input type="text" class="form-control" id="title" required
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
                            <input type="text" class="form-control" id="desc" required
                                   placeholder="<?= LangManager::translate("news.add.desc_placeholder") ?>"
                                   maxlength="255">
                            <div class="form-control-icon">
                                <i class="fas fa-text-width"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h6><?= LangManager::translate("news.add.image") ?> :</h6>
                        <input required class="mt-2 form-control form-control-sm" type="file" id="image"
                               accept=".png,.jpg,.jpeg,.webp,.svg,.gif">
                        <span><?= LangManager::translate("news.add.allow_files") ?></span>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" value="1" id="comm" checked>
                            <label class="form-check-label" for="comm">
                                <h6><?= LangManager::translate("news.add.enable_comm") ?></h6></label>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" value="1" id="likes" checked>
                            <label class="form-check-label" for="likes">
                                <h6><?= LangManager::translate("news.add.enable_likes") ?></h6></label>
                        </div>
                    </div>
                </div>
                <h6><?= LangManager::translate("news.add.content") ?> :</h6>


                <div>
                    <div class="card-in-card" id="editorjs"></div>
                </div>
                <div class="text-center mt-2">
                    <button id="saveButton" disabled type="submit"
                            class="btn btn-primary"><?= LangManager::translate("news.button.create_before") ?></button>
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
     let input_img = document.querySelector("#image");
     let button = document.querySelector("#saveButton");
     input_title.addEventListener("change", stateHandle);
     input_desc.addEventListener("change", stateHandle);
     input_img.addEventListener("change", stateHandle);
     function stateHandle() {
     if (document.querySelector("#title").value !="" && document.querySelector("#desc").value !="" && document.querySelector("#image").value !="") {
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
                            return fetch("<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER")?>cmw-admin/pages/uploadImage/add", {
                                method: "POST",
                                body: formData
                            }).then(res => res.json())
                                .then(response => {
                                    return {
                                        success: 1,
                                        file: {
                                            url: "<?= EnvManager::getInstance()->getValue("PATH_URL")?>Public/Uploads/Editor/" + response
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
        data: {},
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
                formData.append('title', document.getElementById("title").value);
                formData.append('desc', document.getElementById("desc").value);
                formData.append('image', document.getElementById("image").files[0]);
                formData.append('content', JSON.stringify(savedData));
                formData.append('comm', comm_state.toString());
                formData.append('likes', likes_state.toString());

                fetch("<?= EnvManager::getInstance()->getValue("PATH_URL") ?>cmw-admin/news/add", {
                    method: "POST",
                    body: formData
                })
                
                button.disabled = true;
                button.innerHTML = "<?= LangManager::translate("news.button.saving") ?>";
                setTimeout(() => {
                            button.innerHTML = "<i style='color: #16C329;' class='fa-solid fa-check fa-shake'></i> Ok !";
                        }, 850);
                setTimeout(() => {
                            document.location.replace("<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . 'cmw-admin/news/manage'?>");
                        }, 1000);
            })
            .catch((error) => {
                alert("Error " + error);
            });
    });
</script>