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
use Syracuse\src\modules\models\Login as Model;

class Login extends Controller implements Module {

    private $_moduleName;
    private $_parameters;
    private $_model;
    private $_errors;

    public function __construct(string $moduleName, array $parameters) {
        $this->_moduleName = $moduleName;
        $this->_parameters = $parameters;
        $this->loadGui();

        $this->_errors = [];

        $this->_model = new Model();
    }

    public function execute() : int {
        $this->_model->login($this->_errors);
        return ReturnCode::SUCCESS;
    }

    public function display() : void {
        echo self::$gui->displayTemplate('login', $this->_model->getData() +  ['errors' => $this->_errors]);
    }
}