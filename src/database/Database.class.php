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

/**
 * Class Database
 * @package Syracuse\src\database
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Database {

    /**
     * The connection object
     */
    private static $_connection;

    /**
     * Creates a new instance of the QueryBuilder
     * @param string $action The query type
     * @param string $table The table name
     * @return QueryBuilder A new instance of the QueryBuilder
     */
    public static function interact(string $action, string $table) : QueryBuilder {
        if (!in_array($action, ['retrieve', 'delete', 'modify', 'insert']))
            (new Error('Could not execute query.', 'Unknown action'))->trigger();

        return new QueryBuilder(self::$_connection, $action, $table);
    }

    /**
     * Function to set the connection.
     * @param Connection $connection
     * @return int The return code
     */
    public static function setConnection(Connection $connection) : int {
        if (!empty(self::$_connection))
            return ReturnCode::CANNOT_ENDANGER_CONNECTION;

        self::$_connection = $connection;

        return ReturnCode::SUCCESS;
    }
}