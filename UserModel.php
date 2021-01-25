<?php

namespace Okami\Core;

use Okami\Core\DB\DbModel;

/**
 * Class UserModel
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}