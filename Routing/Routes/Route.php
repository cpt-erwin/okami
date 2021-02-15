<?php

namespace Okami\Core\Routing\Routes;

use LogicException;
use Okami\Core\Interfaces\ExecutableInterface;
use Okami\Core\Response;
use Okami\Core\Traits\WithMiddlewaresTrait;

/**
 * Class Route
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
abstract class Route implements ExecutableInterface
{
    use WithMiddlewaresTrait {
        WithMiddlewaresTrait::addMiddleware as private;
        WithMiddlewaresTrait::addMiddlewares as private;
    }

    /**
     * @var string[]
     */
    private array $paths;

    /** @var array|callable|string $callback */
    private $callback;

    /**
     * @var array
     */
    private array $params = [];

    /**
     * @var array<string, string>
     */
    private array $patterns = [
        'any' => '.*', // Any
        'num' => '[0-9]+', // Numbers
        'alpha' => '[a-zA-Z]+', // Letters
        'alnum' => '[a-zA-Z0-9]+', // Letters & numbers
        'slug' => '[a-zA-Z0-9\-\_]+', // Letters & numbers with dash & underscore signs as dividers
        'id' => '[0-9]+', // Same as :num // FIXME: Is there a way to remove this duplicity?
    ];

    /**
     * Route constructor.
     *
     * @param string $path
     * @param string|callable|array $callback
     */
    public function __construct(string $path, $callback)
    {
        $this->paths = array_reverse($this->analyzePath($path));
        $this->callback = $callback;
    }

    /**
     * @param string $path
     * @param string $root
     *
     * @return string[]
     */
    private function analyzePath(string $path, string $root = ''): array
    {
        $matches = preg_split('/(\[.*?\]$)|(\{.*?\})/', $path, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if ($matches === false) {
            throw new LogicException("Unexpected exception occurred while testing the path against the REGEX.");
        }

        $paths = [];

        if (sizeof($matches) === 1) {
            /** SIMPLE PATH **/
            $paths[] = $matches[0];
        } else {
            /** WILDCARD PATH **/
            foreach ($matches as $match) {
                if (preg_match('/^\{.*?\}$/', $match)) {
                    $param = explode(':', str_replace(['{', '}'], '', $match));
                    if (sizeof($param) === 1) {
                        $param[1] = 'any';
                    }
                    $this->params[] = $param[0];
                    $paths[array_key_last($paths)] .= '(' . $this->getPattern($param[1]) . ')';
                    continue;
                }
                if (preg_match('/^\[.*?\]$/', $match)) {
                    $root = $paths[array_key_last($paths)];
                    $subPaths = $this->analyzePath(substr($match, 1, -1), $root);
                    foreach ($subPaths as $subPath) {
                        $paths[] = $root . $subPath;
                    }
                    continue;
                }

                $paths[] = $match;
            }
        }

        return $paths;
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    private function getPattern(string $pattern): string
    {
        if (!array_key_exists($pattern, $this->patterns)) {
            throw new LogicException('Unknown pattern \'' . $pattern . '\' used!');
        }

        return $this->patterns[$pattern];
    }

    /**
     * @return Response
     */
    abstract public function execute(): Response;

    /**
     * @param string $pathToMatch
     *
     * @return bool
     */
    public function match(string $pathToMatch): bool
    {
        foreach ($this->paths as $path) {
            $pattern = '/^' . str_replace('/', '\/', $path) . '$/';
            if (preg_match($pattern, $pathToMatch)) {
                // FIXME: Make a constant for replacement delimiter @&#&@
                $arguments = explode('@&#&@', preg_replace($pattern, '$1@&#&@$2@&#&@$3@&#&@$4', $pathToMatch));
                $this->params = array_combine($this->params,
                    array_slice($arguments, 0, sizeof($this->params))); // initialize params
                array_walk($this->params, function (&$value) {
                    $value = $value ?: null;
                });

                return true;
            }
        }

        return false;
    }

    /**
     * @param string $middleware
     *
     * @return $this
     */
    public function withMiddleware(string $middleware): Route
    {
        $this->addMiddleware($middleware);

        return $this;
    }

    /**
     * @param array $middlewares
     *
     * @return $this
     */
    public function withMiddlewares(array $middlewares): Route
    {
        $this->addMiddlewares($middlewares);

        return $this;
    }

    /**
     * @return array|callable|string
     */
    protected function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return array
     */
    protected function getParams(): array
    {
        return $this->params;
    }
}