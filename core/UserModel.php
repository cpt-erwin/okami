<?php

namespace Okami\Core;

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