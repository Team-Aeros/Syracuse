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

use PDO;
use PDOException;
use Syracuse\src\core\models\ReturnCode;
use Syracuse\src\errors\Error;

/**
 * Class Connection
 * @package Syracuse\src\database
 */
class Connection {

    /**
     * @var PDO
     */
    private $_pdoConnection;
    /**
     * @var string
     */
    private $_prefix;

    /**
     * Connection constructor.
     * All the necessary information needed
     * to connect to the database.
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $dbname
     * @param string $prefix
     * @param null|string $charset
     */
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
            logError('database', sprintf('An error occurred while connecting to the database (%s)', $e->getMessage()), __FILE__, __LINE__);
            earlyExit('Could not connect to the database.', $e->getMessage());
        }

        $this->_prefix = $prefix;
    }

    /**
     * Function to return the prefix.
     * @return string
     */
    public function getPrefix() : string {
        return $this->_prefix;
    }

    /**
     * This function will execute the query with the associated parameters.
     * @param string $query
     * @param array $params
     * @param bool $hasResults
     * @param array $results
     * @return int
     */
    public function executeQuery(string $query, array $params, bool $hasResults = true, array &$results = []) : int {
        try {
            $preparedQuery = $this->_pdoConnection->prepare($query);

            foreach ($params as $key => $value)
                $preparedQuery->bindValue(':' . $key, $value);

            $preparedQuery->execute();

            if ($hasResults)
                $results = $preparedQuery->fetchAll();
        }

        catch (PDOException $e) {
            logError('database', sprintf('An error occurred while trying to execute a query (%s)', $e->getMessage()), __FILE__, __LINE__);
            (new Error('Could not execute query.', $e->getMessage()))->trigger();
        }

        return ReturnCode::SUCCESS;
    }
}