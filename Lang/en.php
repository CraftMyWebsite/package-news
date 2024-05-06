<?php

return [
    "news" => "News",
    "dashboard" => [
        "title" => "News",
        "title_add" => "Add News",
        "title_edit" => "Edit the News",
        "desc" => "Manage your site's news",
    ],
    "modal" => [
        "delete" => "Delete :",
        "deletealert" => "The deletion is permanent.",
    ],
    "button" => [
        "create_before" => "<i class='fa-solid fa-spinner fa-spin-pulse'></i> Create before save",
        "saving" => "<i class='fa-solid fa-spinner fa-spin-pulse'></i> Saving in progress",
    ],
    "editor" => [
        "start" => "Start typing or click the '+' to choose a block to add...",
    ],
    "list" => [
        "list" => "List of news",
        "table" => [
            "title" => "Title",
            "description" => "Description",
            "author" => "Author",
            "views" => "Views",
            "creation_date" => "creation date",
            "edit" => "Edit",
            "link" => "Link",
        ],
    ],
    "add" => [
        "title" => "News Title",
        "title_placeholder" => "Title",
        "desc" => "Short description of the news",
        "desc_placeholder" => "Description",
        "enable_comm" => "Enable comments",
        "enable_likes" => "Enable likes",
        "allow_files" => "Allowed files : png, jpg, jpeg, webp, svg, gif",
        "image" => "Cover image",
        "select_image" => "Choose cover image",
        "content" => "Content",
        "toasters" => [
            "success" => "News added with success !",
            'error' => 'Unable to add this news !',
        ],
    ],
    "edit" => [
        "toasters" => [
            "success" => "News update with success !",
        ],
    ],
    "delete" => [
        "toasters" => [
            "success" => "Delete remove with success !",
        ],
    ],
    "tags" => [
        "toasters" => [
            "add" => [
                "success" => "Tag ajouté avec succès",
                "error" => "Impossible d'ajouter ce tag",
            ],
            "delete" => [
                "success" => "Tag supprimé avec succès",
                "error" => "Impossible de supprimer ce tag",
            ],
            "edit" => [
                "success" => "Tag modifié avec succès",
                "error" => "Impossible de modifier ce tag",
            ],
        ],
        "add" => [
            'title' => "Ajouter un tag",
        ],
        'list' => [
            'title' => 'Liste des tags',
            'associatedNews' => 'Articles associés',
        ],
        'edit' => [
            'title' => 'Modification du tag',
        ],
        'icon' => "Icon",
        'name' => 'Nom',
        'color' => 'Couleur',
        'tags' => 'Tags',
    ],
    "permissions" => [
        "news" => [
            "add" => "Add news",
            "manage" => "Manage news",
            "edit" => "Edit news",
            "delete" => "Delete news",
        ],
    ],
];