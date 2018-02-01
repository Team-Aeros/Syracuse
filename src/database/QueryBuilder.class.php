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

    /**
     * Creates a new instance of the query builder. This method is called automatically by the Database class.
     * @param Connection $connection The database connection
     * @param string $action The desired action, i.e. delete, update, retrieve or insert
     * @param string $table The name of thet able you want to interact with
     */
    public function __construct(Connection $connection, string $action, string $table) {
        $this->_connection = $connection;
        $this->_action = $action;
        $this->_table = $table;
        $this->_errors = [];

        $this->_fields = [];
        $this->_conditions = [];
        $this->_params = [];
    }

    /**
     * What fields should be loaded? Leave empty to load all fields (not recommended, though).
     * @param \string[] ...$fields The fields you need
     * @return QueryBuilder
     */
    public function fields(string ...$fields) : self {
        $this->_fields = $fields ?? [];

        return $this;
    }

    /**
     * This method is used for adding simple 'where' clauses. Each array element requires two child elements, the
     * first one being the key and the second one being the value. If you need more sophisticated conditions, such
     * as 'between' and 'is equal to or higher than', have a look at the whereCustom() method instead.
     * @param \array[] ...$conditions The conditions in the format described above
     * @return QueryBuilder
     */
    public function where(array ...$conditions) : self {
        foreach ($conditions as $condition) {
            if ($condition[1][0] != ':')
                $this->_conditions[] = sprintf('`%s` = \'%s\'', $condition[0], $condition[1]);
            else
                $this->_conditions[] = sprintf('`%s` = %s', $condition[0], $condition[1]);
        }

        return $this;
    }

    /**
     * Allows the developer to specify custom conditions. Conditions need to be in the following format:
     * ['key = :value']
     * @param string|\string[] ...$conditions The conditions
     * @return QueryBuilder
     */
    public function whereCustom(string ...$conditions) : self {
        $this->_conditions = array_merge($this->_conditions, $conditions);

        return $this;
    }

    /**
     * Orders a table by a single column.
     * @param string $fieldName The column you want to use for sorting the table
     * @param string $direction The sorting direction. Should be 'ASC' or 'DESC'. Please don't mess this up.
     * @return QueryBuilder
     */
    public function orderBy(string $fieldName, string $direction) : self {
        $this->_order = $fieldName . ' ' . $direction;

        return $this;
    }

    /**
     * Orders a table by multiple columns. Each value should be in the following format: 'fieldname ASC' or 'fieldname DESC'
     * @param \string[] ...$fields The columns you want to sort on.
     * @return QueryBuilder
     */
    public function orderByMultiple(string ...$fields) : self {
        $this->_order = $fields;

        return $this;
    }

    /**
     * Is there a maximum number of records that should be returned?
     * @param int $max The maximum number of records
     * @return QueryBuilder
     */
    public function max(int $max) : self {
        $this->_limitMax = $max;

        return $this;
    }

    /**
     * This method allows you to return records X to X.
     * @param int $min When to start returning records
     * @param int $max When to stop returning records
     * @return QueryBuilder
     */
    public function boundaries(int $min, int $max) : self {
        $this->_limitMin = $min;
        $this->_limitMax = $max;

        return $this;
    }

    /**
     * Adds a jion to the query. Note: the join value should be added manually.
     * @param string $join The join, e.g. 'LEFT JOIN cake c ON (r.cake_id = c.id)'
     * @return QueryBuilder
     */
    public function join(string $join) : self {
        $this->_joins[] = $join;

        return $this;
    }

    /**
     * Replaces the query with a custom one. Obviously, this will overwrite the existing query (if there is one).
     * @param string $query The raw query.
     * @return QueryBuilder
     */
    public function raw(string $query) : self {
        $this->_query = $query;

        return $this;
    }

    /**
     * Placeholders (e.g. :value, :id) can be added here. Keep in mind you do not have to add the colons yourself,
     * as this will be done automatically. Example: array placeholders = ['id' => 5, 'name' => 'John Doe']. It is
     * STRONGLY recommended to use this method for adding user input to queries, since it is so much safer and takes
     * care of SQL injections automatically.
     * @param array $placeholders
     * @return QueryBuilder
     */
    public function placeholders(array $placeholders) : self {
        $this->_params = $placeholders;

        return $this;
    }

    /**
     * Inserts a single record into the database.
     * @param array $values An associative array ('field' => 'value') containing the desired values
     * @return int An error code
     */
    public function insert(array $values) : int {
        $fields = [];

        foreach (array_keys($values) as $field)
            $fields[] = '`' . $field . '`';

        $this->_query .= sprintf('INSERT INTO %s (%s) VALUES (', $this->_connection->getPrefix() . $this->_table, implode(', ', $fields));
        $this->setValues($values);
        $this->_query .= ');';

        return $this->_connection->executeQuery($this->_query, $this->_params, false);
    }

    /**
     * Generates a list of values used for inserting rows
     * @param array $values An associative array ('field' => 'value') containing the desired values
     * @return void
     */
    private function setValues(array $values) : void {
        $valueCount = count($values);

        $counter = 0;
        foreach ($values as $key => $value) {
            if (is_numeric($value) || $value[0] == ':')
                $this->_query .= sprintf('%s', (string) $value);
            else
                $this->_query .= sprintf('\'%s\'', $value);

            if ($counter < $valueCount - 1)
                $this->_query .= ', ';

            $counter++;
        }
    }

    /**
     * Generates the actual query
     * @param array $values An associative array ('field' => 'value') containing the desired values
     * @return void
     */
    private function generateQuery(?array $values = []) : void {
        switch ($this->_action) {
            case 'retrieve':
                $this->_query = 'SELECT ' . (empty($this->_fields) ? '*' : implode(', ', $this->_fields)) . ' FROM ';
                break;
            case 'delete':
                $this->_query = 'DELETE FROM ';
                break;
            case 'update':
                $this->_query = 'UPDATE ';
                break;
            default:
                return;
        }

        $this->_query .= $this->_connection->getPrefix() . $this->_table;

        if ($this->_action == 'update' && !empty($values)) {
            $this->_query .= ' SET ';
            $this->setValues($values);
        }

        if (!empty($this->_joins))
            $this->_query .= ' ' . implode(' ', $this->_joins);

        if (!empty($this->_conditions))
            $this->_query .= ' WHERE ' . implode(' AND ', $this->_conditions);

        if (!empty($this->_order))
            $this->_query .= ' ORDER BY ' . (is_array($this->_order) ? implode(', ', $this->_order) : $this->_order);

        if (!empty($this->_limitMax))
            $this->_query .= ' LIMIT ' . (!empty($this->_limitMin) ? $this->_limitMin . ', ' : '') . $this->_limitMax;
    }

    /**
     * Updates one or more database records
     * @param array $values An associative array ('field' => 'value') containing the desired values
     * @return int An error code
     */
    public function update(array $values) : int {
        if (empty($this->_query))
            $this->generateQuery($values);

        return $this->_connection->executeQuery($this->_query, $this->_params, false);
    }

    public function delete() : int {
        if (empty($this->_query))
            $this->generateQuery();

        return $this->_connection->executeQuery($this->_query, $this->_params, false);
    }

    /**
     * Returns an array of one or more records that meet the conditions.
     * @return array The results
     */
    public function getAll() : array {
        if (empty($this->_query))
            $this->generateQuery();

        $results = [];

        if ($this->_action != 'retrieve') {
            $error = 'Invalid action. You can only invoke the getAll() method on an object with query type \'retrieve\'';
            $this->_errors[] = $error;
            logError('database', $error, __FILE__, __LINE__);

            return [];
        }
        else if (empty($this->_query)) {
            $this->_errors[] = 'Could not generate query.';
            logError('database', 'Could not generate query (query was empty)', __FILE__, __LINE__);

            return [];
        }

        $this->_connection->executeQuery($this->_query, $this->_params, true, $results);

        return $results ?? [];
    }

    /**
     * Returns a single row from a table, based on the conditions. If there are multiple records that meet these
     * conditions, only the first one is returned. Note: since there is only one row, the array returned by this
     * method can be used directly, without having to loop over it.
     * @return array The record that meets the conditions
     */
    public function getSingle() : array {
        $results = $this->getAll();

        foreach ($results as $result)
            return $result;

        return [];
    }

    /**
     * Checks if there were any errors. If so, an error code is returned. Not sure when this should be used, but
     * you might find this useful.
     * @return int The return code
     */
    public function getReturnCode() : int {
        return empty($this->_errors) ? ReturnCode::SUCCESS : ReturnCode::DATABASE_ERROR;
    }

    /**
     * @return $this->_errors
     */
    public function getErrors() : array {
        return $this->_errors;
    }

    /**
     * Converts the query to a string.
     * @return string The query (in string format)
     */
    public function __toString() : string {
        return $this->_query;
    }
}