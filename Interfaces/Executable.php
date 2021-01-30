<?php

namespace Okami\Core\Interfaces;

use Okami\Core\Response;

interface Executable
{
    public function execute(): Response;
}