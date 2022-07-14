<?php

use CMW\Controller\News\NewsController;
use CMW\Router\Router;


/** @var $router Router Main router */

//Admin pages
$router->scope('/cmw-admin/news', function($router) {

    $router->get('/add', "news#addNews");
    $router->post('/add', "news#addNewsPost");

    $router->get('/list', "news#listNews");

    $router->get('/edit/:id', function($id) {
        (new NewsController())->editNews($id);
    })->with('id', '[0-9]+');

    $router->post('/edit/:id', function($id) {
        (new NewsController())->editNewsPost($id);
    })->with('id', '[0-9]+');

    $router->get('/delete/:id', function($id) {
        (new NewsController())->deleteNews($id);
    })->with('id', '[0-9]+');

});