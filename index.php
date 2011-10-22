<?php

require_once 'settings.php';
require_once 'dal/MySQLDatabase.php';
require_once 'libs/Smarty.class.php';

// connect to database
$database = new MySQLDatabase(DATABASE_TYPE, DATABASE_HOSTNAME,
		DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_DATABASE, DATABASE_PORT);

// get tables
$tables = $database->getTables();

// for every table...
foreach ($tables as $table) {
    // get it's columns
    $columns = $database->getRecords('SHOW COLUMNS FROM ' . $table);

    // prepare vars
    foreach ($columns as &$column) {
	// make fieldname camel cased
	$column['Field'] = toCamelCase($column['Field']);
    }
}

/**
 * Convert a string to camelcasing.
 *
 * @return	string					The resulted string
 * @param	string $inputValue			The string that should be camelcased
 * @param	string[optional] $separator		The separator between the words
 * @param	bool[optional] $firstCharLowerCase	Should the first character be lowercase?
 */
function toCamelCase($inputValue, $separator = '_', $firstCharLowerCase = false) {
    // init var
    $returnValue = '';

    // fetch words
    $words = explode((string) $separator, (string) $inputValue);

    foreach ($words as $i => $word) {
	// skip empty words
	if ($word != '') 
	{
	    // make word lowercase
	    $word = strtolower($word);

	    // When it's not the first word and we shouldn't use lowercase for the first word convert first letter to uppercase
	    if ($i != 0 && !$firstCharLowerCase) $word = ucfirst($word);

	    // append the word to the return value
	    $returnValue .= $word;
	}
    }

    // return resulting string
    return $returnValue;
}

?>
