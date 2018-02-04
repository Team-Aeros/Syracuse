<?php
/**
 * Created by PhpStorm.
 * User: Jelmer
 * Date: 04-Feb-18
 * Time: 11:43
 */
use Syracuse\src\core\models\ReturnCode;
use Syracuse\src\headers\{Controller, Module};
use Syracuse\src\modules\models\Login as Model;
use Syracus\src\DataGetter\DataGetter as DataGetter;
class Update extends Controller implements Module {

    private $_moduleName;
    private $_parameters;
    private $_model;
    private $_rainData;

    public function __construct(string $moduleName, array $parameters) {
        $this->_moduleName = $moduleName;
        $this->_parameters = $parameters;

        $dataGetter = new DataGetter();
        $this->_rainData = $dataGetter->getRainDataFiles();

        $this->loadGui();
        $this->loadAuthenticationSystem();

        $this->_model = new Model();
    }

    public function execute() : int {
        return ReturnCode::SUCCESS;
    }

    public function display() : void {
        echo json_encode($this->_rainData);
    }
}