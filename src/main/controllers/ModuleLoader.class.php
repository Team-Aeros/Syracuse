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

namespace Syracuse\src\main\controllers;

use Syracuse\src\core\controllers\Route;
use Syracuse\src\headers\Controller;

class ModuleLoader extends Controller {

    private $_moduleName;
    private $_routeParams;
    private $_module;


    private const DEFAULT_MODULE = 'login';


    private static $_modules = [
        'main' => 'Main',
        'login' => 'Login',
        'logout' => 'Main',
        'help' => 'Help'
    ];

    public function __construct(Route $route) {
        $this->_moduleName = $route->getRouteInfo()['module_name'] ?? '';
        $this->_routeParams = $route->getRouteInfo()['parameters'] ?? [];

        $this->loadSettings();
    }

    public function load() : void {
        if (!self::moduleExists($this->_moduleName) && empty($this->_routeParams['ajax_request']))
            $this->_moduleName = self::DEFAULT_MODULE;
        else if (!self::moduleExists($this->_moduleName))
            earlyExit('Could not load module.', 'To prevent recursion, the script was halted.');

        $module = 'Syracuse\src\modules\controllers\\' . self::$_modules[$this->_moduleName];

        if (!file_exists(self::$config->get('path') . '/src/modules/controllers/' . self::$_modules[$this->_moduleName] . '.class.php'))
            earlyExit('Could not load module.', 'The ' . $this->_moduleName . ' module controller could not be found.');

        $this->_module = new $module($this->_moduleName, $this->_routeParams);
    }

    public function getModule() : Controller {
        return $this->_module;
    }

    public static function moduleExists(string $module) : bool {
        return array_key_exists($module, self::$_modules);
    }
}