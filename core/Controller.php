<?php

abstract class Controller
{

    protected $controllerName;
    protected $actionName;
    protected $application;
    protected $request;
    protected $response;
    protected $session;
    protected $dbManager;

    public function __construct($application)
    {
        $this->controllerName = strtolower(substr(get_class($this), 0, -10));

        $this->application = $application;
        $this->request = $application->getRequest();
        $this->response = $application->getResponse();
        $this->session = $application->getSession();
        $this->dbManager = $application->getDBManager();
    }
    
    public function run($action, $params = array())
    {
        $this->actionName = $action;

        $actionMethod = $action . 'Action';
        if (!method_exists($this, $actionMethod)) {
            $this->forward404();
        }

        $content = $this->$actionMethod($params);

        return $content;
    }

    public function render($variables = array(), $template = null, $layout = 'layout')
    {
        $defaults = array(
            'request' => $this->request,
            'baseUri' => $this->request->getBaseUri(),
            'session' => $this->session,
        );

        $view = new View($this->application->getViewDir(), $defaults);

        if (is_null($template)) {
            $template = $this->actionName;
        }

        $path = $this->controllerName . '/' . $template;

        return $view->render($path, $variables, $layout);

    }

    public function forward404()
    {
        throw new HttpNotFoundException('Forwarded 404 page from' . $this->controllerName . '/' . $this->actionName);
    }

    protected function redirect($uri)
    {
        if (!preg_match('#http?://#', $uri)) {
            $protocol = $this->request->isSsl() ? 'https://' : 'http://';
            $host = $this->request->getHost();
            $baseUri = $this->request->getBaseUri();

            $uri = $protocol . $host . $baseUri . $uri;
        }

        $this->response->setStatusCode(302, 'Found');
        $this->response->setHttpHeader('Location', $uri);
    }
}
