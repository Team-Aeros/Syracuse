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
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Connection {

    /**
     * The PDO connection
     */
    private $_pdoConnection;

    /**
     * The database prefix
     */
    private $_prefix;

    /**
     * Connection constructor.
     * All the necessary information needed
     * to connect to the database.
     * @param string $host The database server
     * @param string $username The database username
     * @param string $password The database password
     * @param string $dbname The database name
     * @param string $prefix The database prefix
     * @param null|string $charset The character set
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
     * @return string _prefix
     */
    public function getPrefix() : string {
        return $this->_prefix;
    }

    /**
     * This function will execute the query with the associated parameters.
     * @param string $query The query
     * @param array $params Database parameters
     * @param bool $hasResults Whether or not there should be any results
     * @param array $results An empty array to put results in
     * @return int The return code
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