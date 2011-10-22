<?php

/**
 * This class provides most of the base methods implemented by almost
 * every database system
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Davy Hellemans <davy@spoon-library.com>
 */
interface Database {

    /**
     * Query to delete records, which returns the number of affected rows.
     *
     * @return	int								The number of affected rows.
     * @param	string $table					The table to perform the delete-statement on.
     * @param	string[optional] $where			The WHERE-clause.
     * @param	mixed[optional] $parameters		The parameters that will be used in the query.
     */
    public function delete($table, $where = null, $parameters = array());

    /**
     * Drops one or more tables.
     *
     * @param	mixed $tables		The table(s) to drop.
     */
    public function drop($tables) ;

    /**
     * Executes a query.
     *
     * @param	string $query					The query to execute, only use with queries that don't return a result.
     * @param	mixed[optional] $parameters		The parameters that will be used in the query.
     */
    public function execute($query, $parameters = array());

    /**
     * Retrieve a single column.
     *
     * @return	array							An array with the values from a single column
     * @param	string $query					The query, specify maximum one field in the SELECT-statement.
     * @param	mixed[optional] $parameters		The parameters that will be used in the query.
     */
    public function getColumn($query, $parameters = array());

    /**
     * Retrieve the debug setting
     *
     * @return	bool	true if debug is enabled, false if not.
     */
    public function getDebug();

    /**
     * Fetch the name of the database driver
     *
     * @return	string	The name of the driver that is used.
     */
    public function getDriver();

    /**
     * Retrieves the possible ENUM values
     *
     * @return	array			An array with all the possible ENUM values.
     * @param	string $table	The table that contains the ENUM field.
     * @param	string $field	The name of the field.
     */
    public function getEnumValues($table, $field);

    /**
     * Retrieve the raw database instance (PDO object)
     *
     * @return	PDO	The PDO-instance.
     */
    public function getHandler();

    /**
     * Retrieve the number of rows in a result set
     *
     * @return	int								The number of rows in the result-set.
     * @param	string $query					Teh query to perform.
     * @param	mixed[optional] $parameters		The parameters that will be used in the query.
     */
    public function getNumRows($query, $parameters = array());

    /**
     * Retrieve the results of 2 columns as an array key-value pair
     *
     * @return	array							An array with the first column as key, the second column as the values.
     * @param	string $query					The query to perform.
     * @param	mixed[optional] $parameters		The parameters that will be used in the query.
     */
    public function getPairs($query, $parameters = array());

    /**
     * Fetch the executed queries
     *
     * @return	array	An array with all the executed queries, will only be filled in debug-mode.
     */
    public function getQueries();

    /**
     * Retrieve a single record
     *
     * @return	mixed							An array containing one record. Keys are the column-names.
     * @param	string $query					The query to perform. If multiple rows are selected only the first row will be returned.
     * @param	mixed[optional] $parameters		The parameters that will be used in the query.
     */
    public function getRecord($query, $parameters = array());

    /**
     * Retrieves an associative array or returns null if there were no results
     *
     * @return	mixed							An array containing arrays which represent a record.
     * @param	string $query					The query to perform.
     * @param	mixed[optional] $parameters		The parameters that will be used in the query.
     * @param	string[optional] $key			The field that should be used as key, make sure this is unique for each row.
     */
    public function getRecords($query, $parameters = array(), $key = null);

    /**
     * Retrieve the tables in the current database
     *
     * @return	array	An array containg a list of all available tables.
     */
    public function getTables();

    /**
     * Retrieve the type for this value
     *
     * @return	int
     * @param	mixed $value		The value to retrieve the type for.
     */
    private function getType($value);

    /**
     * Fetch a single var
     *
     * @return	string							The value as a string
     * @param	string $query					The query to perform.
     * @param	mixed[optional] $parameters		The parameters that will be used in the query.
     */
    public function getVar($query, $parameters = array());

    /**
     * Inserts one or more records
     *
     * @return	int				The last inserted ID.
     * @param	string $table	The table wherein the record will be inserted.
     * @param	array $values	The values for the record to insert, keys of this array should match the column names.
     */
    public function insert($table, array $values);

    /**
     * Optimize one or more tables
     *
     * @param	mixed $tables	An array containing the name(s) of the tables to optimize.
     */
    public function optimize($tables);

    /**
     * Quote the name of a table or column.
     * Note: for now this will only put backticks around the name (mysql).
     *
     * @return	string			The quoted name.
     * @param	string $name	The name of a column or table to quote.
     */
    protected function quoteName($name);

    /**
     * Retrieves an associative array or returns null if there were no results
     * This is an alias for getRecords
     *
     * @return	mixed							An array containing arrays which represent a record.
     * @param	string $query					The query to perform.
     * @param	mixed[optional] $parameters		The parameters that will be used in the query.
     * @param	string[optional] $key			The field that should be used as key, make sure this is unique for each row.
     */
    public function retrieve($query, $parameters = array(), $key = null);

    /**
     * Set database name
     *
     * @param	string $database	The name of the database.
     */
    private function setDatabase($database);

    /**
     * Set the debug status
     *
     * @param	bool[optional] $on	Should debug-mode be activated. Be carefull, this will use a lot of resources (Memory and CPU).
     */
    public function setDebug($on = true);

    /**
     * Set driver type
     *
     * @param	string $driver	The driver to use. Available drivers depend on your server configuration.
     */
    private function setDriver($driver);

    /**
     * Set hostname
     *
     * @param	string $hostname	The host or IP of your database-server.
     */
    private function setHostname($hostname);

    /**
     * Set password
     *
     * @param	string $password	The password to authenticate on your database-server.
     */
    private function setPassword($password);

    /**
     * Set port
     *
     * @param	int $port	The port to connect on.
     */
    private function setPort($port);

    /**
     * Set username
     *
     * @param	string $username	The username to authenticate on your database-server.
     */
    private function setUsername($username);

    /**
     * Truncate on or more tables
     *
     * @param	mixed $tables	A string or array containing the list of tables to truncate.
     */
    public function truncate($tables);

    /**
     * Builds a query for updating records
     *
     * @return	int								The number of affected rows.
     * @param	string $table					The table to run the UPDATE-statement on.
     * @param	array $values					The values to update, use the key(s) as columnnames.
     * @param	string[optional] $where			The WHERE-clause.
     * @param	mixed[optional] $parameters		The parameters that will be used in the query.
     */
    public function update($table, array $values, $where = null, $parameters = array());
}

?>