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

abstract class ControllerHeader {

    protected $config;

    protected function loadSettings() : void {
        $this->config = Registry::retrieve('config');
    }
}