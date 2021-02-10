<?php

namespace Okami\Core;

use Exception;

/**
 * Class Logger
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class Logger
{
    private string $logFile;

    /**
     * Logger constructor.
     *
     * @param string $logFile
     */
    public function __construct(string $logFile)
    {
        $this->logFile = $logFile;
    }

    /**
     * @param string $message
     *
     * @throws Exception
     */
    public function log(string $message)
    {
        if (file_put_contents($this->logFile, $message, FILE_APPEND) === false) {
            throw new Exception('Couldn\'t log a message!');
        }
    }
}