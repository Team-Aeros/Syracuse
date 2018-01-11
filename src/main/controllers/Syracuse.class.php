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

namespace Syracuse\src\main\controllers;

use Syracuse\Config;
use Syracuse\src\core\models\Registry;
use Syracuse\src\database\{Connection, Database};

class Syracuse {

    private $_gui;
    private $_config;

    public function __construct() {
        $this->_gui = new GUI();

        $this->_config = Registry::store('config', new Config());
    }

    public function start() : void {
        Database::setConnection(new Connection(
            $this->_config->get('database_server'),
            $this->_config->get('database_username'),
            $this->_config->get('database_password'),
            $this->_config->get('database_name'),
            $this->_config->get('database_prefix')
        ));

        $this->_config->wipeSensitiveData();

        // !! debug
        $errors = [];
        $results = [];

        $results = Database::interact('retrieve', 'station')
            ->fields('stn', 'country')
            ->where(['country', 'netherlands'])
            ->orderBy('country', 'ASC')
            ->getAll();

        $returnCode = Database::interact('insert', 'station')->insert([
            'name' => 'Nieuw station',
            'country' => 'Nederland'
        ], false);

        print_r($results);
        echo 'Return code: ', $returnCode;

        // NOTE: Template and language loading should be done BEFORE this constant
        define('LOADED_TEMPLATE_AND_LANG', true);
    }
}