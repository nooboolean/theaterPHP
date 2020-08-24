<?php

class Request {
    
    public function isPost(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }

        return false;
    }

    // TODO:getyRequestBody()の処理とまとめる
    public function getQueryParam($key, $default = null){
        $paramValue = $_GET[$key];
        if (isset($paramValue)) {
            return $paramValue;
        }
        return $default;
    }

    public function getRequestBody($key, $default = null){
        $paramValue = $_POST[$key];
        if (isset($paramValue)) {
            return $paramValue;
        }
        return $default;
    }

    public function getHost(){
        return !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    }

    public function isSsl(){
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }
        return false;
    }

    public function getRequestUri(){
        return $_SERVER['REQUEST_URI'];
    }

    public function getBaseUri(){
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $requestUri = $this->getRequestUri();
        if (strpos($requestUri, $scriptName) === 0) {
            return $scriptName;
        } else if (strpos($requestUri, dirname($scriptName) === 0)) {
            return rtrim(dirname($scriptName, '/'));
        }
        return '';
    }

    public function getPathInfo(){
        $requestUri = $this->getRequestUri();
        $baseUri = $this->getBaseUri();

        $paramPointer = strpos($requestUri, '?');
        if ($paramPointer !== false) {
            $requestUri = substr($requestUri, 0, $paramPointer);
        }

        return (string)substr($requestUri, strlen($baseUri));
    }
}