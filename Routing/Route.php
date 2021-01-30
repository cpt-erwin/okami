<?php

namespace Okami\Core\Routing;

use LogicException;
use Okami\Core\Interfaces\ExecutableInterface;
use Okami\Core\Middlewares\Middleware;
use Okami\Core\Response;

/**
 * Class Route
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
abstract class Route implements ExecutableInterface
{
    /** @var Middleware[] */
    public array $middlewares = [];

    private array $paths;

    /** @var array|callable|string $callback */
    private $callback;

    private array $params = [];

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

    private function analyzePath(string $path, string $root = ''): array
    {
        // paths example
        // ''
        // '/'
        // '/posts'
        // '/posts/{id}'
        // '/posts/{id:id}'
        // '/posts[/{id:id}]'
        // '/gallery/{galleryID:id}/image/{imageID:id}'
        // '/gallery/{galleryID:id}[/image/{imageID:id}]'
        // '/stock/{stockID:id}[/supplier/{supplierID:id}[/product/{productID:id}]]'

        $matches = preg_split('/(\[.*?\]$)|(\{.*?\})/', $path, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if($matches === false) {
            throw new LogicException("Unexpected exception occurred while testing the path against the REGEX.");
        }

        $paths = [];

        if (sizeof($matches) === 1) {
            /** SIMPLE PATH **/
            $paths[] = $matches[0];
        } else {
            /** WILDCARD PATH **/

            // When the route like this occurs
            // '/gallery/{galleryID:id}[/image/{imageID:id}]'
            // we should generate two routes starting from the last optional one to first mandatory
            // 1) '/gallery/{galleryID:id}/image/{imageID:id}'
            // 2) '/gallery/{galleryID:id}'
            // for the second case we still want imageID to be set with value null


            foreach ($matches as $match) {
                // If $match starts with { and ends with } then remove those signs
                // and split string by : where first element of the returned array
                // will be the name of the param and the second element will be its
                // regex pattern to be matched.
                // If the array has only one element the :any pattern will be selected.
                if (preg_match('/^\{.*?\}$/', $match)) {
                    $param = explode(':', str_replace(['{', '}'], '', $match));
                    if(sizeof($param) === 1) {
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

    abstract public  function execute(): Response;

    public function match(string $pathToMatch): bool
    {
        foreach($this->paths as $path) {
            $pattern = '/^' . str_replace('/', '\/', $path) . '$/';
            if(preg_match($pattern, $pathToMatch)) {
                // FIXME: Make a constant for replacement delimiter @&#&@
                $arguments = explode('@&#&@', preg_replace($pattern, '$1@&#&@$2@&#&@$3@&#&@$4', $pathToMatch));
                $this->params = array_combine($this->params, array_slice($arguments, 0, sizeof($this->params))); // initialize params
                array_walk($this->params, function(&$value) { $value = $value ?: null; }); // set empty params to null value
                return true;
            }
        }
        return false;
    }

    public function hasMiddlewares(): bool
    {
        return !empty($this->middlewares);
    }

    /**
     * @return array|callable|string
     */
    protected function getCallback()
    {
        return $this->callback;
    }

    protected function getParams(): array
    {
        return $this->params;
    }

    private function getPattern(string $pattern): string
    {
        if(!array_key_exists($pattern, $this->patterns)) {
            throw new LogicException('Unknown pattern \'' . $pattern . '\' used!');
        }
        return $this->patterns[$pattern];
    }

    public function addMiddleware(string $middlewareClass): Route
    {
        $this->middlewares[] = $middlewareClass;
        return $this;
    }

    public function addMiddlewares(array $middlewareClasses): Route
    {
        foreach ($middlewareClasses as $middlewareClass) {
            $this->withMiddleware($middlewareClass);
        }
        return $this;
    }
}