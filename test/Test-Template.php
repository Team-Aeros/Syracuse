<?php

use Syracuse\Config;
use Syracuse\src\core\models\Registry;

require 'Test.class.php';
require '../autoload.php';

class TemplateTest extends Test {

    private $_config;

    public function __construct() {
        preg_match('/^[a-zA-Z_]*$/', 'cake', $match0);
        preg_match('/^[a-zA-Z_]*$/', 'cake()', $match1);
        preg_match('/^[a-zA-Z_]*$/', 'cake(\'i_have_parameters\')', $match2);
        preg_match('/^[a-zA-Z_]*$/', 'cake(\'i\', \'have\', \'multiple\', \'parameters\')', $match3);
        
        preg_match('/^([a-zA-Z_].+)*\((.*)\)$/', 'cake', $match4);
        preg_match('/^([a-zA-Z_].+)*\((.*)\)$/', 'cake()', $match5);
        preg_match('/^([a-zA-Z_].+)*\((.*)\)$/', 'cake(\'i_have_parameters\')', $match6);
        preg_match('/^([a-zA-Z_].+)*\((.*)\)$/', 'cake(\'i\', \'have\', \'multiple\', \'parameters\')', $match7);

        $this->methods = [
            [($match0[0] ?? '') == 'cake', __FILE__, __LINE__],
            [($match1[0] ?? '') != 'cake', __FILE__, __LINE__],
            [($match2[0] ?? '') != 'cake', __FILE__, __LINE__],
            [($match3[0] ?? '') != 'cake', __FILE__, __LINE__],

            [($match4[0] ?? '') != 'cake', __FILE__, __LINE__],
            [($match5[0] ?? '') == 'cake()', __FILE__, __LINE__],
            [($match6[0] ?? '') == 'cake(\'i_have_parameters\')', __FILE__, __LINE__],
            [($match7[0] ?? '') == 'cake(\'i\', \'have\', \'multiple\', \'parameters\')', __FILE__, __LINE__]
        ];
    }
}

$test = new TemplateTest();
$test->run();