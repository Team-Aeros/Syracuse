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

namespace Syracuse\src\database;

use Syracuse\src\core\models\ReturnCode;
use Syracuse\src\errors\Error;

class Database {

    private static $_connection;

    public static function interact(string $action, string $table) : QueryBuilder {
        // @todo Replace with the Error class
        if (!in_array($action, ['retrieve', 'delete', 'modify', 'insert']))
            (new Error('Could not execute query.', 'Unknown action'))->trigger();

        return new QueryBuilder(self::$_connection, $action, $table);
    }

    public static function setConnection(Connection $connection) : int {
        if (!empty(self::$_connection))
            return ReturnCode::CANNOT_ENDANGER_CONNECTION;

        self::$_connection = $connection;

        return ReturnCode::SUCCESS;
    }
}