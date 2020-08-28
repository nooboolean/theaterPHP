<?php

abstract class Application
{

    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $dbManager;

    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    public function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    public function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->dbManager = new DBManager();
        $this->router = new Router($this->registerRoutes());
    }

    public function configure()
    {
    }

    abstract public function getRootDir();

    abstract protected function registerRoutes();

    public function isDebugMode()
    {
        return $this->debug;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getDBManager()
    {
        return $this->dbManager;
    }

    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    public function run()
    {
        $params = $this->router->resolve($this->request->getPathInfo());
        if ($params == false) {
        }

        $controller = $params['controller'];
        $action = $params['action'];

        $this->runAction($controller, $action, $params);

        $this->response->send();
    }

    public function runAction($controllerName, $action, $params = array())
    {
        $controllerClass = ucfirst($controllerName) . 'Controller';

        $controller = $this->findController($controllerClass);
        if ($controller == false) {
        }

        $content = $controller->run($action, $params);
        $this->response->setContent($content);
    }

    public function findController($controllerClass)
    {
        if (!class_exists($controllerClass)) {
            $controllerFile = $this->getControllerDir() . '/' . $controllerClass . 'php';
            if (!is_readable($controllerFile)) {
                return false;
            } else {
                require_once $controllerFile;

                if (!class_exists($controllerClass)) {
                    return false;
                }
            }
        }
        return new $controllerClass($this);
    }

}
