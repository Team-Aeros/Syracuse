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

abstract class Controller {

    protected static $config;
    protected static $gui;

    protected function loadSettings() : void {
        self::$config = Registry::retrieve('config');
    }

    protected function loadGui() : void {
        self::$gui = Registry::retrieve('gui');
    }
}