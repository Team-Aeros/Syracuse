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

class QueryBuilder {

    private $_connection;
    private $_action;
    private $_errors;
    private $_table;

    private $_fields;
    private $_conditions;
    private $_order;

    private $_limitMax;
    private $_limitMin;

    private $_params;

    public function __construct(Connection $connection, string $action, string $table) {
        $this->_connection = $connection;
        $this->_action = $action;
        $this->_table = $table;
        $this->_errors = [];

        $this->_fields = [];
    }

    public function fields(...$fields) : self {
        $this->_fields = $fields ?? [];

        return $this;
    }

    public function where(array $conditions) : self {

        return $this;
    }

    public function orderBy(string $fieldName, int $direction) : self {

        return $this;
    }

    public function orderByMultiple(array $fields) : self {

        return $this;
    }

    public function max(int $max) : self {
        $this->_limitMax = $max;

        return $this;
    }

    public function boundaries(int $min, int $max) : self {
        $this->_limitMin = $min;
        $this->_limitMax = $max;

        return $this;
    }

    private function generateQuery() : ?string {
        $query = '';

        switch ($this->_action) {
            case 'retrieve':
                $query = 'SELECT ' . (empty($this->_fields) ? '*' : implode(', ', $this->_fields)) . ' FROM ';
                break;
            default:
                return null;
        }

        $query .= $this->_table;

        return null;
    }

    public function getAll() : array {
        $query = $this->generateQuery();
        $results = [];

        if ($this->_action != 'retrieve') {
            $this->_errors[] = 'Invalid action. You can only invoke the getAll() method on an object with query type \'retrieve\'';
            return [];
        }
        else if (empty($query)) {
            $this->_errors[] = 'Could not generate query.';
            return [];
        }

        $this->_connection->executeQuery($query, $this->_params, $this->_errors, $results);

        return $results ?? [];
    }

    public function getReturnCode() : int {
        return empty($this->_errors) ? ReturnCode::SUCCESS : ReturnCode::DATABASE_ERROR;
    }

    public function getErrors() : array {
        return $this->_errors;
    }
}