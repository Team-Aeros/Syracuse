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

/**
 * Class Model
 * @package Syracuse\src\headers
 */
abstract class Model {


    protected static $config;


    protected function loadSettings() : void {
        self::$config = Registry::retrieve('config');
    }

    /**
     * Function for returning data.
     * @return array
     */
    public function getData() : array {
        return [];
    }
}