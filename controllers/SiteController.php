<?php

namespace Okami\Controllers;

use Okami\Core\App;

/**
 * Class SiteController
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Controllers
 */
class SiteController
{
    public function home()
    {
        $params = [
            'name' => "TuMiSoft"
        ];
        return App::$app->router->renderView('home', $params);
    }

    public function contact()
    {
        return App::$app->router->renderView('contact');
    }

    public function handleContact()
    {
        return "Handling submitted data...";
    }
}