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

/**
 * Class Login
 * @package Syracuse\src\modules\controllers
 */
class Login extends Controller implements Module {

    private $_moduleName;
    private $_parameters;
    private $_model;
    private $_errors;

    /**
     * Login constructor.
     * @param string $moduleName
     * @param array $parameters
     */
    public function __construct(string $moduleName, array $parameters) {
        $this->_moduleName = $moduleName;
        $this->_parameters = $parameters;

        $this->loadGui();
        $this->loadAuthenticationSystem();

        $this->_errors = [];

        $this->_model = new Model();
    }

    /**
     * Displays loggin errors if there are any and checks if the user is logged in to redirect if he/she is.
     * @return int, the return code
     */
    public function execute() : int {
        $this->_model->login($this->_errors);
        if (self::$auth->isLoggedIn())
            $this->redirectTo('/');

        return ReturnCode::SUCCESS;
    }

    /**
     * displays the login template and gives as parameters the handler, the model data and the error array
     */
    public function display() : void {
        self::$gui->displayTemplate('login', $this->_model->getData() +  ['errors' => $this->_errors]);
    }
}