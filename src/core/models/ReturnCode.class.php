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
 * Return codes
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class ReturnCode {
    // No error occurred
    public const SUCCESS = 0x00;

    // A general error
    public const GENERAL_ERROR = 0x01;

    // When a page cannot be accessed because the user is logged in/out or does not have the right permissions
    public const PERMISSION_DENIED = 0x02;

    // When a record (e.g. a reading) cannot be found
    public const RECORD_NOT_FOUND = 0x03;

    // Overwriting the connection object _should_ trigger this error
    public const CANNOT_ENDANGER_CONNECTION = 0x04;

    // A database error occurred
    public const DATABASE_ERROR = 0x05;

    // Invalid query type
    public const INVALID_QUERY_TYPE = 0x06;

    // Features that haven't been implemented (yet)
    public const NOT_IMPLEMENTED = 0xFF;
}