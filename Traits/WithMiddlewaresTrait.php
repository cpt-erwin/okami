<?php

namespace Okami\Core\Traits;


/**
 * Class WithMiddlewaresTrait
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 */
trait WithMiddlewaresTrait
{
    private array $middlewares = [];

    public function hasMiddlewares(): bool
    {
        return !empty($this->middlewares);
    }

    public function addMiddleware(string $middlewareClass)
    {
        $this->middlewares[] = $middlewareClass;
    }

    public function addMiddlewares(array $middlewareClasses)
    {
        foreach ($middlewareClasses as $middlewareClass) {
            $this->addMiddleware($middlewareClass);
        }
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}