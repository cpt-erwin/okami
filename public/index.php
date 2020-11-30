<?php

use Okami\Core\App;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App(dirname(__DIR__));

$app->router->get('/', 'home');
$app->router->get('/contact', 'contact');

$app->run();