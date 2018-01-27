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
 * @package Syracuse\src\database
 */
class Route {

    private $_dispatcher;
    private $_requestMethod;
    private $_routeInfo;
    private $_uri;

    public function __construct() {
        $this->_dispatcher = new Router();
        $this->_dispatcher->addRoute('GET', '/help', 'help');
        $this->_dispatcher->addRoute('GET', '/logout', 'logout');
        $this->_dispatcher->addRoute('GET', '/download', 'download');
        $this->_dispatcher->addRoute(['POST', 'GET'], '/login', 'login');

        $this->_dispatcher->addRoute('GET', '/help/ajax/{ajax_request}', 'help');

        $this->_requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->setRequestUri();
        $this->_routeInfo = $this->_dispatcher->dispatch($this->_requestMethod, $this->_uri);
    }

    private function setRequestUri() : void {
        $this->_uri = $_SERVER['REQUEST_URI'];

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

        $this->_uri = rawurldecode(rtrim($this->_uri, ' /'));
    }

    public function getRouteInfo() : array {
        if ($this->_routeInfo[0] == Router::RETURN_FOUND) {
            $routeInfo = [
                'module_name' => $this->_routeInfo[1],
                'parameters' => $this->_routeInfo[2]
            ];
        }

        return $routeInfo ?? [];
    }
}