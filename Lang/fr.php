<?php

return [
    "news" => "Actualités",
    "dashboard" => [
        "title" => "Actualités",
        "title_add" => "Ajouter une actualités",
        "title_edit" => "Modifier une actualités",
        "desc" => "Gérez les actualités de votre site",
    ],
    "modal" => [
        "delete" => "Supression de :",
        "deletealert" => "La supression est definitive.",
    ],
    "button" => [
        "create_before" => "<i class='fa-solid fa-spinner fa-spin-pulse'></i> Créer pour enregistrer",
        "saving" => "<i class='fa-solid fa-spinner fa-spin-pulse'></i> Enregistrement en cours ...",
    ],
    "editor" => [
        "start" => "Commencez à taper ou cliquez sur le '+' pour choisir un bloc à ajouter...",
    ],
    "list" => [
        "list" => "Actualités",
        "table" => [
            "title" => "Titre",
            "description" => "Description",
            "author" => "Auteur",
            "views" => "Affichages",
            "creation_date" => "Date de création",
            "edit" => "Modifications",
            "link" => "Lien",
        ],
    ],
    "add" => [
        "title" => "Titre de l'actualité",
        "title_placeholder" => "Titre",
        "desc" => "Courte description de l'actualité",
        "desc_placeholder" => "Description",
        "enable_comm" => "Activer les commentaires",
        "enable_likes" => "Activer les likes",
        "allow_files" => "Fichiers autorisés : png, jpg, jpeg, webp, svg, gif",
        "image" => "Image de couverture",
        "select_image" => "Choisissez une image",
        "content" => "Contenu",
        "toasters" => [
            "success" => "Actualité ajoutée avec succès !",
            'error' => "Impossible d'ajouter cette news !",
        ],
    ],
    "edit" => [
        "toasters" => [
            "success" => "Actualité mis à jour avec succès !",
        ],
    ],
    "delete" => [
        "toasters" => [
            "success" => "Actualité supprimée avec succès !",
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
            'title' => 'Tags',
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
            "add" => "Ajouter",
            "manage" => "Gérer",
            "edit" => "Modifier",
            "delete" => "Supprimer",
        ],
    ],
];