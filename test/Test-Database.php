<?php

use Syracuse\Config;
use Syracuse\src\core\models\Registry;
use Syracuse\src\database\{Connection, Database};

require 'Test.class.php';
require '../autoload.php';

class DatabaseTest extends Test {

    private $_config;

    public function __construct() {
        $this->_config = Registry::store('config', new Config());

        Database::setConnection(new Connection(
            $this->_config->get('database_server'),
            $this->_config->get('database_username'),
            $this->_config->get('database_password'),
            $this->_config->get('database_name'),
            $this->_config->get('database_prefix')
        ));

        $this->_config->wipeSensitiveData();

        $this->methods = [
            [$this->_config->get('database_server') === 'hidden', __FILE__, __LINE__],
            [$this->_config->get('database_username') === 'hidden', __FILE__, __LINE__],
            [$this->_config->get('database_password') === 'hidden', __FILE__, __LINE__],
            [$this->_config->get('database_name') === 'hidden', __FILE__, __LINE__],
        ];
    }
}

$test = new DatabaseTest();
$test->run();