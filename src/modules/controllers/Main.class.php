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

namespace Syracuse\src\modules\controllers;

use Syracuse\src\core\models\ReturnCode;
use Syracuse\src\headers\{Controller, Module};

class Main extends Controller implements Module {

    private $_moduleName;
    private $_parameters;

    public function __construct(string $moduleName, array $parameters) {
        $this->_moduleName = $moduleName;
        $this->_parameters = $parameters;
    }

    public function execute() : int {
        return ReturnCode::NOT_IMPLEMENTED;
    }

    public function display() : void {

    }
}