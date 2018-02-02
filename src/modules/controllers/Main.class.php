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
use Syracuse\src\modules\models\Main as Model;
use Syracuse\src\download\DataReader as DataReader ;

class Main extends Controller implements Module {

    private $_moduleName;
    private $_parameters;
    private $_model;

    public function __construct(string $moduleName, array $parameters) {
        $dataReader = new DataReader();
        if ($moduleName == 'logout') {
            $this->loadAuthenticationSystem();
            self::$auth->logOut();
            exit;
        }

        if ($moduleName == 'download') {
            $dataReader->download();
            exit;
        }
        /* call the update function of $dataReader every minute
           this gives back an array where Top10Rain is [0] and the temperatures is [1]
        */
        $this->_moduleName = $moduleName;
        $this->_parameters = $parameters;
        $this->loadGui();



        $this->_model = new Model();
    }

    public function execute() : int {
        return ReturnCode::SUCCESS;
    }

    public function display() : void {
        self::$gui->displayTemplate('main', $this->_model->getData());
    }
}