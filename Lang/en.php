<?php

return [
    'news' => 'News',
    'dashboard' => [
        'title' => 'News',
        'title_add' => 'Add News',
        'title_edit' => 'Edit the News',
        'desc' => "Manage your site's news",
    ],
    'modal' => [
        'delete' => 'Delete :',
        'deletealert' => 'The deletion is permanent.',
    ],
    'button' => [
        'create_before' => "<i class='fa-solid fa-spinner fa-spin-pulse'></i> Create before save",
        'saving' => "<i class='fa-solid fa-spinner fa-spin-pulse'></i> Saving in progress",
    ],
    'editor' => [
        'start' => "Start typing or click the '+' to choose a block to add...",
    ],
    'list' => [
        'list' => 'News',
        'table' => [
            'title' => 'Title',
            'description' => 'Description',
            'author' => 'Author',
            'views' => 'Views',
            'creation_date' => 'creation date',
            'edit' => 'Edit',
            'link' => 'Link',
            'delete_selected' => 'Delete selected',
        ],
    ],
    'add' => [
        'title' => 'News Title',
        'title_placeholder' => 'Title',
        'desc' => 'Short description of the news',
        'desc_placeholder' => 'Description',
        'enable_comm' => 'Enable comments',
        'enable_likes' => 'Enable likes',
        'status_toggle' => 'Publish',
        'allow_files' => 'Allowed files : png, jpg, jpeg, webp, svg, gif',
        'image' => 'Cover image',
        'select_image' => 'Choose cover image',
        'content' => 'Content',
        'toasters' => [
            'success' => 'News added with success !',
            'error' => 'Unable to add this news !',
        ],
        'scheduled_date' => 'Scheduled publishing date',
        'scheduled_date_placeholder' => 'Leave empty for immediate publishing',
        'scheduled_date_help' => "The news will be automatically published at that time <br> if cron jobs are enabled in the news settings.<br>Scheduled articles remain in draft until published." ,
    ],
    'edit' => [
        'toasters' => [
            'success' => 'News update with success !',
        ],
    ],
    'delete' => [
        'toasters' => [
            'success' => 'Delete remove with success !',
        ],
    ],
    'tags' => [
        'toasters' => [
            'add' => [
                'success' => 'Tag added successfully',
                'error' => 'Unable to add this tag',
            ],
            'delete' => [
                'success' => 'Tag deleted successfully',
                'error' => 'Unable to remove this tag',
            ],
            'edit' => [
                'success' => 'Tag modified successfully',
                'error' => 'Unable to modify this tag',
            ],
        ],
        'add' => [
            'title' => 'Add a tag',
        ],
        'list' => [
            'title' => 'Tags',
            'associatedNews' => 'Related articles',
        ],
        'edit' => [
            'title' => 'Editing the tag',
        ],
        'icon' => 'Icon',
        'name' => 'Name',
        'color' => 'Color',
        'tags' => 'Tags',
    ],
    'permissions' => [
        'news' => [
            'add' => 'Add',
            'manage' => 'Manage',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ],
    ],
    'menu' => [
        'title' => 'News',
        'settings' => 'Settings',
        'news' => 'News',
    ],
    'settings' => [
        'cron_info' => 'If this option is enabled, scheduled publications will be published automatically via a cron job. Make sure your server runs cron jobs regularly for this feature to work properly.',
        'documentation_link' => 'Documentation link:',
        'scheduled_publications' => 'Scheduled publications',
        'enable_cron_toggle' => 'Enable cron jobs for scheduled publications',
        'cron_url_label' => 'Cron URL for scheduled publications',
        'copy_url' => 'Copy URL',
        'cron_url_help' => 'Copy this URL and configure it in your cron job manager to execute scheduled publications.',
    ],
];
