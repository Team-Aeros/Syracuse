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

use PDO;
use PDOException;

class Connection {

    private $_pdoConnection;
    private $_prefix;

    public function __construct(string $host, string $username, string $password, string $dbname, string $prefix, ?string $charset = 'utf8mb4') {
        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ];

            $this->_pdoConnection = new PDO(
                sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $dbname, $charset),
                $username,
                $password,
                $options
            );
        }

        catch (PDOException $e) {
            earlyExit('Could not connect to the database', $e->getMessage());
        }

        $this->_prefix = $prefix;
    }

    public function getPrefix() : string {
        return $this->_prefix;
    }

    public function executeQuery(string $query, array $params) : void {
        // execute the query
    }
}