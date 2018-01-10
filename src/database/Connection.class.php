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
use Syracuse\src\core\models\ReturnCode;

class Connection {

    private $_pdoConnection;
    private $_prefix;

    public function __construct(string $host, string $username, string $password, string $dbname, string $prefix, ?string $charset = 'utf8mb4') {
        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
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

    public function executeQuery(string $query, array $params, array &$errors, array &$results = []) : int {
        try {
            $preparedQuery = $this->_pdoConnection->prepare($query);

            foreach ($params as $key => $value)
                $preparedQuery->bindValue(':' . $key, $value);

            $preparedQuery->execute();
            $results = $preparedQuery->fetchAll();
        }

        catch (PDOException $e) {
            $errors[] = $e->getMessage();
            return ReturnCode::DATABASE_ERROR;
        }

        return ReturnCode::SUCCESS;
    }
}