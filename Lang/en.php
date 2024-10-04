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
        ],
    ],
    'add' => [
        'title' => 'News Title',
        'title_placeholder' => 'Title',
        'desc' => 'Short description of the news',
        'desc_placeholder' => 'Description',
        'enable_comm' => 'Enable comments',
        'enable_likes' => 'Enable likes',
        'allow_files' => 'Allowed files : png, jpg, jpeg, webp, svg, gif',
        'image' => 'Cover image',
        'select_image' => 'Choose cover image',
        'content' => 'Content',
        'toasters' => [
            'success' => 'News added with success !',
            'error' => 'Unable to add this news !',
        ],
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
    'menu' => 'News',
];
