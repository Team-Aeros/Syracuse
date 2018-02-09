<?php
namespace Syracuse\src\core\controllers;
/**
 * Class Router
 * Writen by Aeros Development because whe couldn't use FastRoute
 * @package Syracuse\src\core\controllers
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Router {

    /**
     * An array containing the routes
     */
    private $routes = array();

    /**
     * Return found
     */
    public const RETURN_FOUND = 0x00;

    /**
     * Route not found
     */
    public const RETURN_NOT_FOUND = 0x01;

    /**
     * Adds a route
     * @param string|array $requestMethod, GET and/or POST
     * @param string $request The URL
     * @param string $handler The handler for the request
     * @return void
     */
    public function addRoute($requestMethod, $request, $handler) {
        $id = sizeof($this->routes);
        $request = array_slice(explode("/", $request), 1);
        $route = array($id, $requestMethod, $request, $handler);
        $this->routes[] = $route;
    }

    /**
     * Dispatches the browser to the correct route
     * @param string|array $requestMethod, GET and/or POST
     * @param string $uri the URL
     * @return array|int Return code, the route, its parameters
     */
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