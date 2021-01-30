<?php

namespace Okami\Core\Interfaces;

use Okami\Core\Response;

interface ExecutableInterface
{
    public function execute(): Response;
}