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
use Syracuse\src\modules\models\Help as Model;

/**
 * Displays the help page.
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Help extends Controller implements Module {

    /**
     * The name of the module
     */
    private $_moduleName;

    /**
     * URL parameters
     */
    private $_parameters;

    /**
     * The model
     */
    private $_model;

    /**
     * Creates a new instance of the Help class
     * @param string $moduleName The name of the module
     * @param array $parameters URL parameters
     */
    public function __construct(string $moduleName, array $parameters) {
        $this->_moduleName = $moduleName;
        $this->_parameters = $parameters;
        $this->loadGui();

        $this->_model = new Model();
    }

    /**
     * Starts execution of the Help module
     * @return int The return code
     */
    public function execute() : int {
        return ReturnCode::SUCCESS;
    }

    /**
     * Displays the help page itself
     * @return void
     */
    public function display() : void {
        self::$gui->displayTemplate('help', $this->_model->getData());
    }
}