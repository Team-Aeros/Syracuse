<?php

/**
 * Syracuse
 *
 * @version     1.0 Beta 1
 * @author      Team Aeros
 * @copyright   2017, Syracuse
 * @since       1.0 Beta 1
 *
 * @license     MIT
 */

namespace Syracuse\src\main\controllers;

use Syracuse\Config;
use Syracuse\src\core\models\Registry;

class Syracuse {

    private $_gui;

    public function __construct() {
        $this->_gui = new GUI();

        Registry::store('config', new Config());
    }

    public function start() : void {

    }
}