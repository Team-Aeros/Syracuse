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

/**
 * Class ModuleLoader
 * @package Syracuse\src\main\controllers
 */
class ModuleLoader extends Controller {

    private $_moduleName;
    private $_routeParams;
    private $_module;

    private const DEFAULT_MODULE = 'main';

    private static $_modules = [
        'main' => 'Main',
        'login' => 'Login',
        'logout' => 'Main',
        'help' => 'Help',
        'download' => 'Main',
        'update' => 'Update'
    ];

    /**
     * ModuleLoader constructor.
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->_moduleName = $route->getRouteInfo()['module_name'] ?? '';
        $this->_routeParams = $route->getRouteInfo()['parameters'] ?? [];

        $this->loadSettings();
    }

    public function load() : void {
        if (!self::moduleExists($this->_moduleName) && empty($this->_routeParams['ajax_request']))
            $this->_moduleName = self::DEFAULT_MODULE;
        else if (!self::moduleExists($this->_moduleName)) {
            /**
             * Just in case this isn't obvious: when a module isn't found, the main module
             * is loaded. Imagine if the main module was the one calling this non-existing
             * module. An infinite loop would be created. For this reason, the script is
             * stopped.
             */
            logError('module', sprintf('Could not find the %s module. Since this is an AJAX request, the script was halted.', $this->_moduleName));
            earlyExit('Could not load module.', 'To prevent recursion, the script was halted.');
        }

        $module = 'Syracuse\src\modules\controllers\\' . self::$_modules[$this->_moduleName];

        if (!file_exists(self::$config->get('path') . '/src/modules/controllers/' . self::$_modules[$this->_moduleName] . '.class.php')) {
            logError('module', sprintf('Could not load the %s module, as its controller could not be found', $this->_moduleName), __FILE__, __LINE__);
            earlyExit('Could not load module.', 'The ' . $this->_moduleName . ' module controller could not be found.');
        }

        $this->_module = new $module($this->_moduleName, $this->_routeParams);
    }

    /**
     * Function for returning the module.
     * @return Controller
     */
    public function getModule() : Controller {
        return $this->_module;
    }

    public static function moduleExists(string $module) : bool {
        return array_key_exists($module, self::$_modules);
    }
}