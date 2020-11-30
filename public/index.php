<?php

use Okami\Core\App;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App();

$app->router->get('/', function () {
    return 'Hello World!';
});

$app->run();