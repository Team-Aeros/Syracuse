<?php

/**
 * Syracuse
 *
 * @version     1.0 Beta 1
 * @author      Aeros Development
 * @copyright   2017-2018 Syracuse
 * @since       1.0 Beta 1
 *
 * @license     MIT
 */

namespace Syracuse\src\core\controllers;

use FastRoute;

/**
 * This class is used for loading the current route. Credits go to nikic at github.com, since this class
 * is based on his example usage code: https://github.com/nikic/FastRoute
 * @package Syracuse\src\database
 */
class Route {

    private $_dispatcher;
    private $_requestMethod;
    private $_routeInfo;
    private $_uri;

    public function __construct() {
        $this->_dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $routeCollector) {
            $routeCollector->addRoute(['POST', 'GET'], '/help/ajax/{ajax_request}', 'help');

            $routeCollector->addRoute('GET', '/help', 'help');
            $routeCollector->addRoute('GET', '/logout', 'logout');
            $routeCollector->addRoute('GET', '/download', 'download');
            $routeCollector->addRoute(['POST', 'GET'], '/login', 'login');

            $routeCollector->addRoute('GET', '/', 'main');
        });

        $this->_requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->setRequestUri();
        $this->_routeInfo = $this->_dispatcher->dispatch($this->_requestMethod, $this->_uri);
    }

    private function setRequestUri() : void {
        $this->_uri = $_SERVER['REQUEST_URI'];

        // Subdirectories are a little more difficult, as Fastroute does not support them by default
        $subDirectories = explode('/', parse_url($this->_uri, PHP_URL_PATH));
        $location = '';

        foreach ($subDirectories as $subDirectory) {
            if (stripos($subDirectory, 'index.php') !== false)
                break;
            else if (empty($subDirectory))
                continue;
            else
                $location .= '/' . $subDirectory;
        }

        $this->_uri = str_ireplace($location . '/index.php', '', $this->_uri);

        if ($pos = strpos($this->_uri, '?') !== false)
            $this->_uri = substr($this->_uri, 0, $pos);

        $this->_uri = rawurldecode(rtrim($this->_uri));
    }

    public function getRouteInfo() : array {
        if ($this->_routeInfo[0] == FastRoute\Dispatcher::FOUND) {
            $routeInfo = [
                'module_name' => $this->_routeInfo[1],
                'parameters' => $this->_routeInfo[2]
            ];
        }

        return $routeInfo ?? [];
    }
}