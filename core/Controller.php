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
    protected $authActions = array();
    const CLASSNAMESUFFIX = 10; //「Controller」の文字数

    public function __construct($application)
    {
        $this->controllerName = strtolower(substr(get_class($this), 0, -self::CLASSNAMESUFFIX));

        $this->application = $application;
        $this->request = $application->getRequest();
        $this->response = $application->getResponse();
        $this->session = $application->getSession();
        $this->dbManager = $application->getDBManager();
    }
    
    public function run($action, $params = array())
    {
        $this->actionName = $action;

        $actionMethod = (string)$action;
        if (!method_exists($this, $actionMethod)) {
            $this->forward404();
        }

        if ($this->needsAuthentication($action) && !$this->session->isAuthenticated()) {
            throw new UnAuthorizedActionException();
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

    protected function generateCsrfToken($formName)
    {
        $key = 'csrf_tokens/' . $formName;
        $tokens = $this->session->get($key, array());
        if (count($tokens) >= 10) {
            array_merge($tokens);
        }

        $token = sha1($formName . session_id() . microtime());
        $tokens[] = $token;

        $this->session->set($key, $tokens);

        return $token;
    }

    protected function checkCsrfToken($formName, $token)
    {
        $key = 'csrf_tokens/' . $formName;
        $tokens = $this->session->get($key, array());

        if (($tokenPointer = array_search($token, $tokens, true)) !== false) {
            unset($tokens[$tokenPointer]);
            $this->session->set($key, $tokens);

            return true;
        }
        return false;
    }

    protected function needsAuthentication($action)
    {
        if ($this->authActions === true || (is_array($this->authActions) && in_array($action, $this->authActions))) {
            return true;
        }

        return false;
    }

}
