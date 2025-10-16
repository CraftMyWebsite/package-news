<?php

return [
    'news' => 'Actualités',
    'dashboard' => [
        'title' => 'Actualités',
        'title_add' => 'Ajouter une actualités',
        'title_edit' => 'Modifier une actualités',
        'desc' => 'Gérez les actualités de votre site',
    ],
    'modal' => [
        'delete' => 'Supression de :',
        'deletealert' => 'La supression est definitive.',
    ],
    'button' => [
        'create_before' => "<i class='fa-solid fa-spinner fa-spin-pulse'></i> Créer pour enregistrer",
        'saving' => "<i class='fa-solid fa-spinner fa-spin-pulse'></i> Enregistrement en cours ...",
    ],
    'editor' => [
        'start' => "Commencez à taper ou cliquez sur le '+' pour choisir un bloc à ajouter...",
    ],
    'list' => [
        'list' => 'Actualités',
        'table' => [
            'title' => 'Titre',
            'description' => 'Description',
            'author' => 'Auteur',
            'views' => 'Affichages',
            'creation_date' => 'Date de création',
            'edit' => 'Modifications',
            'link' => 'Lien',
            'delete_selected' => 'Supprimer la sélection',
        ],
    ],
    'add' => [
        'title' => "Titre de l'actualité",
        'title_placeholder' => 'Titre',
        'desc' => "Courte description de l'actualité",
        'desc_placeholder' => 'Description',
        'enable_comm' => 'Activer les commentaires',
        'enable_likes' => 'Activer les likes',
        'status_toggle' => 'Publier',
        'allow_files' => 'Fichiers autorisés : png, jpg, jpeg, webp, svg, gif',
        'image' => 'Image de couverture',
        'select_image' => 'Choisissez une image',
        'content' => 'Contenu',
        'toasters' => [
            'success' => 'Actualité ajoutée avec succès !',
            'error' => "Impossible d'ajouter cette news !",
        ],
        'scheduled_date' => 'Date de publication programmée',
        'scheduled_date_placeholder' => 'Laissez vide pour une publication immédiate',
        'scheduled_date_help' => "L'actualité sera automatiquement publiée à ce moment-là <br> si les tâches cron sont activées dans les paramètres des actualités.<br>Les articles programmés restent en brouillon jusqu'à leur publication.",
    ],
    'edit' => [
        'toasters' => [
            'success' => 'Actualité mis à jour avec succès !',
        ],
    ],
    'delete' => [
        'toasters' => [
            'success' => 'Actualité supprimée avec succès !',
        ],
    ],
    'tags' => [
        'toasters' => [
            'add' => [
                'success' => 'Tag ajouté avec succès',
                'error' => "Impossible d'ajouter ce tag",
            ],
            'delete' => [
                'success' => 'Tag supprimé avec succès',
                'error' => 'Impossible de supprimer ce tag',
            ],
            'edit' => [
                'success' => 'Tag modifié avec succès',
                'error' => 'Impossible de modifier ce tag',
            ],
        ],
        'add' => [
            'title' => 'Ajouter un tag',
        ],
        'list' => [
            'title' => 'Tags',
            'associatedNews' => 'Articles associés',
        ],
        'edit' => [
            'title' => 'Modification du tag',
        ],
        'icon' => 'Icon',
        'name' => 'Nom',
        'color' => 'Couleur',
        'tags' => 'Tags',
    ],
    'permissions' => [
        'news' => [
            'add' => 'Ajouter',
            'manage' => 'Gérer',
            'edit' => 'Modifier',
            'delete' => 'Supprimer',
        ],
    ],
    'menu' => [
        'title' => 'Actualités',
        'settings' => 'Paramètres',
        'news' => 'Actualités',
    ],
    'settings' => [
        'cron_info' => 'Si cette option est activée, les publications programmées seront publiées automatiquement via une tâche cron. Assurez-vous que votre serveur exécute régulièrement les tâches cron pour que cette fonctionnalité fonctionne correctement.',
        'documentation_link' => 'Lien de documentation :',
        'scheduled_publications' => 'Publications programmées',
        'enable_cron_toggle' => 'Activer les tâches cron pour les publications programmées',
        'cron_url_label' => 'URL du cron pour les publications programmées',
        'copy_url' => 'Copier l\'URL',
        'cron_url_help' => 'Copiez cette URL et configurez-la dans votre gestionnaire de tâches cron pour exécuter les publications programmées.',
        'slug_prefix_label' => 'Préfixe de l\'URL des actualités',
        'slug_prefix_help' => 'Choisissez le préfixe utilisé dans l\'URL pour accéder à vos actualités (ex : /news/, /blog/, /actu/)',
        'toasters' => [
            'save' => [
                'success' => 'Paramètres enregistrés avec succès.',
                'error' => 'Une erreur est survenue lors de l\'enregistrement des paramètres.',
            ],
            'cron_token' => [
                'error' => 'Une erreur est survenue lors de la génération du token cron.',
            ],
        ],
    ],
];
