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

/**
 * Class Language
 * @package Syracuse\src\main\controllers
 */
class Language extends Controller {

    private $_language;
    private $_strings;

    private const STRING_NOT_FOUND = 'Error: language string not found';

    public function __construct() {
        $this->loadSettings();

        $this->_language = new Model(self::$config->get('language'));

        $this->load();
    }

    public function read(string $identifier, string ...$params) : string {
        $str = $this->_strings[$identifier] ?? self::STRING_NOT_FOUND;

        if ($str == self::STRING_NOT_FOUND)
            logError('language', sprintf('Call to undefined language string %s', $identifier), __FILE__, __LINE__);

        return !empty($params) ? sprintf($str, ...$params) : $str;
    }

    public function load() : void {
        if (file_exists($langFile = self::$config->get('path') . '/lang/' . $this->_language->getCode() . '.lang.php')) {
            $this->_strings = require_once($langFile);
        }

        else {
            logError('language', 'Could not load the language files (is the path correct?)', __FILE__, __LINE__);
            earlyExit('Could not load language files.');
        }
    }
}