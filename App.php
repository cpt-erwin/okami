<?php

namespace Okami\Core;

use Exception;
use LogicException;
use Okami\Core\DB\Database;
use Okami\Core\Interfaces\ExecutableInterface;
use Okami\Core\Routing\Router;
use Okami\Core\Traits\WithMiddlewaresTrait;

/**
 * Class App
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class App
{
    use WithMiddlewaresTrait;

    public static string $ROOT_DIR;

    private bool $debug;

    public string $layout = 'main';
    public string $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public View $view;
    public ?UserModel $user = null;
    public static App $app;
    public ?Controller $controller = null;
    private array $callstack = [];

    public function __construct(string $rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->debug = $config['debug'] ?: false;

        if($this->debug) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }

        $this->db = new Database($config['db']);
        $this->userClass = $config['userClass'];

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        }
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        // FIXME: Call App Middleware
        try {
            $response = $this->router->resolve();
            echo $response->body;
        } catch (Exception $e) {
            if($this->debug) {
                throw $e;
            } else {
                $this->response->setStatusCode($e->getCode());
                echo $this->view->renderView('_error', [
                    'exception' => $e
                ]);
            }
        }
    }

    /**
     * @return ?Controller
     */
    public function getController(): ?Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     */
    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function login(UserModel $user): bool
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest(): bool
    {
        return !self::$app->user;
    }

    /**
     * @param ExecutableInterface $executable
     *
     * @throws LogicException
     */
    public function setCallstack(ExecutableInterface $executable)
    {
        if (empty($this->middlewares)) {
            throw new LogicException('Apps Middlewares cannot be empty while using callstack!');
        }

        array_push($this->callstack, $this->middlewares, $executable);
    }

    /**
     * @throws LogicException
     */
    public function executeCallstack(): Response
    {
        if (empty($this->callstack) || is_null($next = array_shift($callstack))) {
            throw new LogicException('Trying to execute an empty callstack!');
        }

        if (is_string($next)) $next = new $next($callstack);

        if (!$next instanceof ExecutableInterface) {
            throw new LogicException('Callstack contains an object which is not an instance of Executable!');
        }

        return $next->execute();
    }
}