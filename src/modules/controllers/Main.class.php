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

/**
 * Displays the widgets.
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Main extends Controller implements Module {

    /**
     * The module name
     */
    private $_moduleName;

    /**
     * URL parameters
     */
    private $_parameters;

    /**
     * An instance of the model
     */
    private $_model;

    /**
     * Creates a new instance of the Main class
     * @param string $moduleName The name of the module
     * @param array $parameters URL parameters
     */
    public function __construct(string $moduleName, array $parameters) {
        if ($moduleName == 'logout') {
            $this->loadAuthenticationSystem();
            self::$auth->logOut();
            exit;
        }

        if ($moduleName == 'download') {
            $dataReader = new DataReader();
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

    /**
     * Starts the execution of this module.
     * @return int The return code
     */
    public function execute() : int {
        return ReturnCode::SUCCESS;
    }

    /**
     * Echoes the template.
     * @return void
     */
    public function display() : void {
        self::$gui->displayTemplate('main', $this->_model->getData());
    }
}