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

namespace Syracuse\src\core\models;

/**
 * Used for reading and validating $_POST and $_GET requests
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Request {

    /**
     * The key
     */
    private $_key;

    /**
     * The value of $_REQUEST[$_KEY]
     */
    private $_value;

    /**
     * Length is valid
     */
    public const VALID_LENGTH = 0;

    /**
     * Value is too short
     */
    public const TOO_SHORT = -1;

    /**
     * Value is too long
     */
    public const TOO_LONG = 1;

    /**
     * Creates a new instance of the Request class and loads the appropriate value
     * @param string $key The request identifier
     */
    public function __construct(string $key) {
        $this->_key = $key;

        if (isset($_REQUEST[$key]))
            $this->_value = $_REQUEST[$key];
    }

    /**
     * Is the value empty?
     * @return bool Whether or not the value is empty
     */
    public function isEmpty() : bool {
        return empty($this->_value);
    }

    /**
     * @return string _key
     */
    public function getKey() : string {
        return $this->_key;
    }

    /**
     * @return string _value
     */
    public function getValue() : string {
        return $this->_value;
    }

    /**
     * Returns the value length
     * @return int The length of the value
     */
    public function getLength() : int {
        return strlen($this->_value);
    }

    /**
     * Whether or not the value is numeric
     * @return bool Whether or not the value is numeric
     */
    public function isNumeric() : bool {
        return is_numeric($this->_value);
    }

    /**
     * Escapes HTML and returns the result
     * @return string The escaped HTML
     */
    public function escapeHTML() : string {
        return htmlspecialchars($this->_value, ENT_NOQUOTES, 'UTF-8');
    }

    /**
     * Removes HTML tags and returns the result
     * @return string The value without HTML tags
     */
    public function stripTags() : string {
        return strip_tags($this->_value);
    }

    /**
     * Verifies the length of the value.
     * @param int $min The minimum length
     * @param int $max The maximum length
     * @return int A success code (see class constants)
     */
    public function verifyLength(int $min, int $max) : int {
        $length = $this->getLength();

        return $length >= $min && $length <= $max ? self::VALID_LENGTH : $length < $min ? self::TOO_SHORT : self::TOO_LONG;
    }
}