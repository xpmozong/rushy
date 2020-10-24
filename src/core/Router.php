<?php

namespace Rushy\Core;

class Router
{
    protected $routes;

    public static function load($file)
    {
        $router = new self;
        require $file;
        return $router;
    }

    public function addRoute($methodTypes, $route, $controller)
    {
        $this->routes[$route]['method'] = $methodTypes;
        $this->routes[$route]['controller'] = $controller;
    }

    public function direct($uri, $methodType)
    {
        if (array_key_exists($uri, $this->routes)) {
            if (in_array($methodType, $this->routes[$uri]['method'])) {
                return $this->callAction(
                    ...explode('@', $this->routes[$uri]['controller'])
                );
            } else {
                throw new \Exception('不支持此URI请求方法');
            }
        } else {
            throw new \Exception('没有为此URI定义路由');
        }
    }

    protected function callAction($controller, $action)
    {
        if (!method_exists($controller, $action)) {
            throw new \Exception("控制器 {$controller} 没有该 {$action} 方法");
        }

        return (new $controller)->$action();
    }
}