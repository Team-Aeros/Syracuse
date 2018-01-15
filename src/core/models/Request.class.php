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

namespace Syracuse\src\core;

class Request {

    private $_key;
    private $_value;

    public const VALID_LENGTH = 0;
    public const TOO_SHORT = -1;
    public const TOO_LONG = 1;

    public function __construct(string $key) {
        $this->_key = $key;

        if (isset($_REQUEST[$key]))
            $this->_value = $_REQUEST[$key];
    }

    public function isEmpty() : bool {
        return empty($this->_value);
    }

    public function getKey() : string {
        return $this->_key;
    }

    public function getValue() : string {
        return $this->_value;
    }

    public function getLength() : int {
        return strlen($this->_value);
    }

    public function isNumeric() : bool {
        return is_numeric($this->_value);
    }

    public function escapeHTML() : string {
        return htmlspecialchars($this->_value, ENT_NOQUOTES, 'UTF-8');
    }

    public function stripTags() : string {
        return strip_tags($this->_value);
    }

    public function verifyLength(int $min, int $max) : int {
        $length = $this->getLength();

        return $length >= $min && $length <= $max ? self::VALID_LENGTH : $length < $min ? self::TOO_SHORT : self::TOO_LONG;
    }
}