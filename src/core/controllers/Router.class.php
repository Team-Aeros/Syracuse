<?php
namespace Syracuse\src\core\controllers;
class Router {

    private $routes = array();

    public const RETURN_FOUND = 0x00;
    public const RETURN_NOT_FOUND = 0x01;

    public function addRoute($requestMethod, $request, $handler) {
        $id = sizeof($this->routes);
        $request = array_slice(explode("/", $request), 1);
        $route = array($id, $requestMethod, $request, $handler);
        $this->routes[] = $route;
    }

    public function dispatch($requestMethod, $uri) {
        foreach ($this->routes as $route) {
            $urlParts = array_slice(explode("/", $uri), 1);
            $request = $route[2];

            $parameters = [];

            if(in_array($requestMethod, is_array($route[1]) ? $route[1] : [$route[1]])) {
                if (empty($urlParts))
                    continue;

                foreach ($urlParts as $key => $part){
                    if (empty($part))
                        continue 2;

                    if (empty($request[$key]) || ($request[$key] != $part && $request[$key][0] != '{' && $request[$key][strlen($request[$key]) - 1] != '}'))
                        continue 2;

                    if ($request[$key][0] == '{' && $request[$key][strlen($request[$key]) - 1] == '}')
                        $parameters[substr($request[$key], 1, strlen($request[$key]) - 2)] = $part;
                }

                return [
                    self::RETURN_FOUND,
                    $route[3],
                    $parameters
                ];
            }
        }

        return self::RETURN_NOT_FOUND;
    }
}