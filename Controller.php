<?php

namespace Okami\Core;

/**
 * Class Controller
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
abstract class Controller
{
    /**
     * @var string
     */
    public string $action = '';

    /**
     * @var string
     */
    private string $layout = 'main';

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }
}