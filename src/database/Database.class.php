<?php

/**
 * Syracuse
 *
 * @version     1.0 Beta 1
 * @author      Team Aeros
 * @copyright   2017, Syracuse
 * @since       1.0 Beta 1
 *
 * @license     MIT
 */

namespace Syracuse\src\database;

use Syracuse\src\core\models\ReturnCode;

class Database {

    private static $_connection;

    public static function store(string $table, array $values) : bool {
        return ReturnCode::NOT_IMPLEMENTED;
    }

    public static function retrieve(string $table, array $parameters) : bool {
        return ReturnCode::NOT_IMPLEMENTED;
    }

    public static function modify(string $table, array $parameters) : bool {
        return ReturnCode::NOT_IMPLEMENTED;
    }

    public static function setConnection(Connection $connection) : bool {
        if (!empty(self::$_connection))
            return ReturnCode::CANNOT_ENDANGER_CONNECTION;

        self::$_connection = $connection;

        return ReturnCode::SUCCESS;
    }
}