<?php

namespace Okami\Core;

use PDO;
use PDOException;

/**
 * Class Database
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\DB
 */
class Database extends PDO
{
    /**
     * Database constructor.
     *
     * @param array $config
     *
     * @throws PDOException
     */
    public function __construct(array $config)
    {
        parent::__construct($config['dsn'] ?? '', $config['user'] ?? '', $config['password'] ?? '');
        $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
    }
}