<?php

namespace Okami\Core;

use PDO;

/**
 * Class Database
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class Database
{
    public PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO($dns, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // If any error occurs throw an exception

    }
}