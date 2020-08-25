<?php

class Router {

    protected $routes;

    public function __construct($routeDef){
        $this->routes = $this->compileRoutes($routeDef);
    }

    // URIの「：」を検知して、正規表現の形にする関数
    // URI内の「:paramName」でクエリパラメータを受け取れるようにしている
    private function compileRoutes($routeDef){
        $routes = array();

        foreach ($routeDef as $uri => $params) {
            $tokens = explode('/', ltrim($uri, '/'));
            foreach ($tokens as $i => $token) {
                if (strpos($token, ':') === 0) {
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]';
                }
                $tokens[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }
        return $routes;
    }

    // 「；」の正規表現とマッチした値を、クエリパラメータの配列とマージする
    public function resolve($pathInfo){
        if (substr($pathInfo, 0, 1) !== '/') {
            $pathInfo = '/' . $pathInfo;
        }

        foreach ($this->routes as $pattern => $params) {
            if (preg_match('#^' . $pattern . '$#', $pathInfo, $matches)) {
                $params = array_merge($params, $matches);
                return $params;
            }
        }

        return false;
    }
}
