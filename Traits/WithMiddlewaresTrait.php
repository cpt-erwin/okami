<?php

namespace Okami\Core\Traits;

/**
 * Class WithMiddlewaresTrait
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 */
trait WithMiddlewaresTrait
{
    /**
     * @var string[]
     */
    private array $middlewares = [];

    /**
     * @return bool
     */
    public function hasMiddlewares(): bool
    {
        return !empty($this->middlewares);
    }

    /**
     * @param string[] $middlewareClasses
     */
    public function addMiddlewares(array $middlewareClasses)
    {
        foreach ($middlewareClasses as $middlewareClass) {
            $this->addMiddleware($middlewareClass);
        }
    }

    /**
     * @param string $middlewareClass
     */
    public function addMiddleware(string $middlewareClass)
    {
        $this->middlewares[] = $middlewareClass;
    }

    /**
     * @return string[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}