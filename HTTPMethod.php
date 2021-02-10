<?php

namespace Okami\Core;

/**
 * Class HTTPMethod
 *
 * FIXME: After rewriting the code to PHP 8.1 change this class to enum
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class HTTPMethod
{
    const GET = 'get';
    const POST = 'post';
    const PUT = 'put';
    const DELETE = 'delete';
    const OPTIONS = 'options';
    const PATCH = 'patch';
}