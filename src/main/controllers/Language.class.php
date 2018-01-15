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

use Syracuse\src\headers\Controller;
use Syracuse\src\main\models\Language as Model;

class Language extends Controller {

    private $_language;
    private $_strings;

    public function __construct() {
        $this->loadSettings();

        $this->_language = new Model(self::$config->get('language'));

        $this->load();
    }

    public function read(string $identifier, string ...$params) : string {
        $str = $this->_strings[$identifier] ?? 'Error: language string not found>';

        return !empty($params) ? sprintf($str, ...$params) : $str;
    }

    public function load() : void {
        if (file_exists($langFile = self::$config->get('path') . '/lang/' . $this->_language->getCode() . '.lang.php')) {
            $this->_strings = require_once($langFile);
        }

        else
            earlyExit('Could not load language files.');
    }
}