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

    private $_joins;

    private $_params;
    private $_query;

    private $_values;

    public function __construct(Connection $connection, string $action, string $table) {
        $this->_connection = $connection;
        $this->_action = $action;
        $this->_table = $table;
        $this->_errors = [];

        $this->_fields = [];
        $this->_conditions = [];
        $this->_params = [];
    }

    public function fields(string ...$fields) : self {
        $this->_fields = $fields ?? [];

        return $this;
    }

    public function where(array ...$conditions) : self {
        foreach ($conditions as $condition)
            $this->_conditions[] = sprintf('`%s` = \'%s\'', $condition[0], $condition[1]);

        return $this;
    }

    /**
     * Allows the developer to specify custom conditions.
     * @param string|\string[] ...$conditions
     * @return QueryBuilder
     */
    public function whereCustom(string ...$conditions) : self {
        $this->_conditions = array_merge($this->_conditions, $conditions);

        return $this;
    }

    public function orderBy(string $fieldName, string $direction) : self {
        $this->_order = $fieldName . ' ' . $direction;

        return $this;
    }

    public function orderByMultiple(string ...$fields) : self {
        $this->_order = $fields;

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

    public function join(string $join) : self {
        $this->_joins[] = $join;

        return $this;
    }

    public function raw(string $query) : self {
        $this->_query = $query;

        return $this;
    }

    public function insert(array $values) : int {
        $fields = [];

        foreach (array_keys($values) as $field)
            $fields[] = '`' . $field . '`';

        $this->_query .= sprintf('INSERT INTO %s (%s) VALUES (', $this->_connection->getPrefix() . $this->_table, implode(', ', $fields));
        $valueCount = count($values);

        $counter = 0;
        foreach ($values as $key => $value) {
            if (is_numeric($value))
                $this->_query .= sprintf('`%s` = %s', $key, (string) $value);
            else
                $this->_query .= sprintf('`%s` = \'%s\'', $key, $value);

            if ($counter < $valueCount - 1)
                $this->_query .= ', ';

            $counter++;
        }

        $this->_query .= ');';

        echo $this->_query;

        return $this->_connection->executeQuery($this->_query, $this->_params, $this->_errors);
    }

    private function generateQuery() : void {
        switch ($this->_action) {
            case 'retrieve':
                $this->_query = 'SELECT ' . (empty($this->_fields) ? '*' : implode(', ', $this->_fields)) . ' FROM ';
                break;
            default:
                return;
        }

        $this->_query .= $this->_connection->getPrefix() . $this->_table;

        if (!empty($this->_joins))
            $this->_query .= ' ' . implode(' ', $this->_joins);

        if (!empty($this->_conditions))
            $this->_query .= ' WHERE ' . implode(' AND ', $this->_conditions);

        if (!empty($this->_order))
            $this->_query .= ' ORDER BY ' . (is_array($this->_order) ? implode(', ', $this->_order) : $this->_order);

        if (!empty($this->_limitMax))
            $this->_query .= ' LIMIT ' . (!empty($this->_limitMin) ? $this->_limitMin . ', ' : '') . $this->_limitMax;
    }

    public function getAll() : array {
        if (empty($this->_query))
            $this->generateQuery();

        $results = [];

        if ($this->_action != 'retrieve') {
            $this->_errors[] = 'Invalid action. You can only invoke the getAll() method on an object with query type \'retrieve\'';
            return [];
        }
        else if (empty($this->_query)) {
            $this->_errors[] = 'Could not generate query.';
            return [];
        }

        $returnCode = $this->_connection->executeQuery($this->_query, $this->_params, $this->_errors, true, $results);

        return $results ?? ['error' => $returnCode];
    }

    public function getReturnCode() : int {
        return empty($this->_errors) ? ReturnCode::SUCCESS : ReturnCode::DATABASE_ERROR;
    }

    public function getErrors() : array {
        return $this->_errors;
    }

    public function __toString() : string {
        return $this->_query;
    }
}