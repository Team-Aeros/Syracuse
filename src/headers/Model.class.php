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
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
abstract class Model {

    /**
     * An instance of the Config class
     */
    protected static $config;

    /**
     * Loads the Config object from the registry
     */
    protected function loadSettings() : void {
        self::$config = Registry::retrieve('config');
    }

    /**
     * Function for returning data.
     * @return array An empty array containing data
     */
    public function getData() : array {
        return [];
    }
}