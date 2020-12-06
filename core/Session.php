<?php

namespace Okami\Core;

/**
 * Class Session
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        // &$flashMessage is a reference to value in $_SESSION[self::FLASH_KEY]
        // thanks to this approach we can modify the code itself in $_SESSION[self::FLASH_KEY]
        foreach ($flashMessages as $key => &$flashMessage) {
            //Mark to be removed
            $flashMessage['remove'] = true;
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash(string $key, string $message)
    {
         $_SESSION[self::FLASH_KEY][$key] = [
             'remove' => false,
             'value' => $message
         ];
    }

    public function getFlash(string $key): ?string
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? null;
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}