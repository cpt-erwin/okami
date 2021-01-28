<?php

namespace Okami\Core;

use Exception;
use Okami\Core\DB\Database;
use Okami\Core\Routing\Router;

/**
 * Class App
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class App
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';

    protected array $eventListeners = [];

    public static string $ROOT_DIR;

    private bool $debug = false;

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

    public function __construct(string $rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->debug = $config['debug'];

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
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try {
            echo $this->router->resolve();
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
        $this->triggerEvent(self::EVENT_AFTER_REQUEST);
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

    public function onEvent(string $eventName, callable $callback)
    {
        $this->eventListeners[$eventName][] = $callback;
    }

    private function triggerEvent(string $eventName)
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach ($callbacks as $callback) {
            call_user_func($callback);
        }
    }
}