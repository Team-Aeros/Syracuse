<?php
/**
 * Created by PhpStorm.
 * User: Jelmer
 * Date: 04-Feb-18
 * Time: 11:43
 */

namespace Syracuse\src\modules\controllers;

use Syracuse\src\core\models\ReturnCode;
use Syracuse\src\headers\{Controller, Module};
use Syracuse\src\modules\models\Login as Model;
use Syracuse\src\DataGetter\DataGetter;

class Update extends Controller implements Module {

    private $_moduleName;
    private $_parameters;
    private $_model;
    private $_rainData;
    private $_tempData;

    public function __construct(string $moduleName, array $parameters) {
        $this->_moduleName = $moduleName;
        $this->_parameters = $parameters;

        $dataGetter = new DataGetter();
        $this->_rainData = $dataGetter->getRainDataFiles();
        $this->_tempData = $dataGetter->getTempDataFiles();


        $this->loadGui();
        $this->loadAuthenticationSystem();

        $this->_model = new Model();
    }

    public function execute() : int {
        return ReturnCode::SUCCESS;
    }

    public function display() : void {
        switch ($this->_parameters['ajax_request'] ?? 'unknown') {
            case 'top10':
                echo json_encode($this->_rainData);
                break;
            case 'tempGraph':
                echo json_encode($this->_tempData);
                break;
            default:
                die(_translate('unknown_ajax_request'));
        }
    }
}
