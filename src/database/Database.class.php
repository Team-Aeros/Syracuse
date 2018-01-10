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

    public const ASC = 0;
    public const DESC = 1;

    public static function interact(string $action, string $table) : QueryBuilder {
        return new QueryBuilder(self::$_connection, $action, $table);
    }

    public static function setConnection(Connection $connection) : int {
        if (!empty(self::$_connection))
            return ReturnCode::CANNOT_ENDANGER_CONNECTION;

        self::$_connection = $connection;

        return ReturnCode::SUCCESS;
    }
}