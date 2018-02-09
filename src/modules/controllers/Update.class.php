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

/**
 * Class Update
 * @package Syracuse\src\modules\controllers
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Update extends Controller implements Module {

    /**
     * The module name
     */
    private $_moduleName;

    /**
     * URL parameters
     */
    private $_parameters;

    /**
     * The update model
     */
    private $_model;

    /**
     * An array containing stations with rain data
     */
    private $_rainData;

    /**
     * An array containing stations with temperature data
     */
    private $_tempData;

    /**
     * An array containing stations, including their coordinates
     */
    private $_stationData;

    /**
     * Update constructor.
     * Uses the DataGetter to get the rain dataset
     * @param string $moduleName The name of the module
     * @param array $parameters The URL parameters
     */
    public function __construct(string $moduleName, array $parameters) {
        $this->_moduleName = $moduleName;
        $this->_parameters = $parameters;

        $dataGetter = new DataGetter();
        $this->_rainData = $dataGetter->getRainDataFiles();
        $this->_tempData = $dataGetter->getTempDataFiles();
        
        if (($this->_parameters['ajax_request'] ?? 'unknown') == 'stations')
            $this->_stationData = $dataGetter->getStations();

        $this->loadGui();
        $this->loadAuthenticationSystem();

        $this->_model = new Model();
    }

    /**
     * Executes the help page module. Currently it doesn't do much.
     * @return int The return code
     */
    public function execute() : int {
        return ReturnCode::SUCCESS;
    }

    /**
     * Echos the json rain data for AJAX to pick up
     * @return void
     */
    public function display() : void {
        switch ($this->_parameters['ajax_request'] ?? 'unknown') {
            case 'top10':
                echo json_encode($this->_rainData);
                break;
            case 'tempGraph':
                echo json_encode($this->_tempData);
                break;
            case 'stations':
                echo json_encode($this->_stationData);
                break;
            default:
                die(_translate('unknown_ajax_request'));
        }
    }
}
