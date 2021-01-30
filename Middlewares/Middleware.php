<?php

namespace Okami\Core\Middlewares;

use Okami\Core\Interfaces\ExecutableInterface;
use Okami\Core\Response;

/**
 * Class Middleware
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Middlewares
 */
abstract class Middleware implements ExecutableInterface
{
    private array $callstack;

    public function __construct(array &$callstack)
    {
        $this->callstack = $callstack;
    }

    public function execute(): Response
    {
        $this->before();

        $next = array_shift($this->callstack);

        if(is_null($next)) {
            throw new \LogicException('Array $callstack must contain an executable as its last element!');
        }

        if(is_string($next)) {
            $next = new $next($this->callstack);
        }

        $response = $next->execute();

        $this->after();

        return $response;
    }

    abstract public function before();
    abstract public function after();
}