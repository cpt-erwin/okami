<?php

namespace Okami\Controllers;

use Okami\Core\Controller;
use Okami\Core\Request;

/**
 * Class SiteController
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Controllers
 */
class SiteController extends Controller
{
    public function home()
    {
        $params = [
            'name' => "TuMiSoft"
        ];
        return $this->render('home', $params);
    }

    public function contact()
    {
        return $this->render('contact');
    }

    public function handleContact(Request $request)
    {
         $body = $request->getBody();

         // TODO: Some $body validation logic...

         return "Handling submitted data...";
    }
}