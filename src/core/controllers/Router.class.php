<?php
namespace Syracuse\src\core\controllers;
class Router {
    private $routes = array();
    public function addRoute($parameters, $request, $handler) {
        $id = sizeof($this->routes);
        $request = explode("/",$request);
        unset($request[0]);
        $route = array($id, $parameters, $request, $handler);
        $this->routes[] = $route;
    }


    public function dispatch($requestMethod, $uri) {
        $found = False;
        foreach ($this->routes as $route ) {
            $parameters = $route[1];
            #print_r($parameters);

            $urlParts = explode("/", $uri);
            unset($urlParts[0]);
            #print_r($urlParts);

            $request = $route[2];
            #print_r($request);

            #print_r($route);

            if(in_array($requestMethod, $parameters)) {
                foreach ($urlParts as $part){
                    $par = array();
                    #print_r($part);
                    if($part[0] == "{" and $part[strlen($part)-1] == "}") {
                        $par[] = substr($part, 1, -1);

                    }
                    #print_r($par);
                    if(!in_array($part, $request)) {
                        break 1;
                    } else {
                        if($part == $urlParts[sizeof($urlParts)]) {
                            $returnArray = array(0, $urlParts[0], $par);
                            return $returnArray;
                        }
                    }
                }
            }

        }
        if(!$found) {
            return array(1);
        }
    }
}