<?php

namespace Okami\Core\Interfaces;

use Okami\Core\Response;

interface ExecutableInterface
{
    /**
     * @return Response
     */
    public function execute(): Response;
}