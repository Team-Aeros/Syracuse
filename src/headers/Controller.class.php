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

namespace Syracuse\src\headers;

use Syracuse\src\core\models\Registry;
use Syracuse\src\main\controllers\GUI;

/**
 * Class Controller
 * @package Syracuse\src\headers
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
abstract class Controller {

    /**
     * Contains an instance of the Config class
     */
    protected static $config;

    /**
     * Contains an instance of the GUI class
     */
    protected static $gui;

    /**
     * Contains an instance of the Auth class
     */
    protected static $auth;

    /**
     * Loads the settings from the registry
     * @return void
     */
    protected function loadSettings() : void {
        self::$config = Registry::retrieve('config');
    }

    /**
     * Loads the GUI object from the registry
     * @return void
     */
    protected function loadGui() : void {
        self::$gui = Registry::retrieve('gui');
    }

    /**
     * Returns the Auth object from the registry
     */
    protected function loadAuthenticationSystem() : void {
        self::$auth = Registry::retrieve('auth');
    }

    /**
     * Redirects to another page (taking the base URL into account)
     * @param string $module The URL (/help, /update/ajax/top10, etc)
     * @return void
     */
    protected function redirectTo(string $module) : void {
        if (empty(self::$config))
            $this->loadSettings();

        header('Location: ' . self::$config->get('url') . '/index.php' . $module);
        die;
    }
}