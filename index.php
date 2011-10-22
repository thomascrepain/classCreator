<?php

require_once 'settings.php';
require_once 'dal/MySQLDatabase.php';
require_once 'libs/Smarty.class.php';

// connect to database
$database = new MySQLDatabase(DATABASE_TYPE, DATABASE_HOSTNAME,
		DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_DATABASE, DATABASE_PORT);

// get tables
echo 'Getting tables from database... ';
$tables = $database->getTables();
echo "DONE \n";

// for every table...
foreach ($tables as $table) {
    echo "Generating classes for $table... ";
    
    // get it's columns
    $columns = $database->getRecords('SHOW COLUMNS FROM ' . $table);

    // prepare vars
    foreach ($columns as &$column) {
	// make fieldname camel cased
	$column['FieldName'] = toCamelCase($column['Field']);
    }

    // prepare template compiler
    $templateCompiler = new Smarty();
    $templateCompiler->setTemplateDir('templates/');
    $templateCompiler->setCompileDir('compiled_templates/');
    $templateCompiler->setConfigDir('configs/');
    $templateCompiler->setCacheDir('cache/');
    
    // assign vars
    $templateCompiler->assign('className', toCamelCase($table, true));
    $templateCompiler->assign('fields', $columns);
    $templateCompiler->assign('authorName', AUTHOR_NAME);
    $templateCompiler->assign('authorEmail', AUTHOR_EMAIL);
    
    // fetch file content
    $fileContent = $templateCompiler->fetch('Class.php');
    
    // save to new file
    $file = fopen('generated_classes/' . toCamelCase($table, true) . '.php', 'w') or die("Couldn't create file: " . toCamelCase($table));
    fwrite($file, $fileContent);
    fclose($file);
    
    echo "DONE \n";
}

/**
 * Convert a string to camelcasing.
 *
 * @return	string					The resulted string
 * @param	string $inputValue			The string that should be camelcased
 * @param	bool[optional] $firstCharUpperCase	Should the first character be uppercase?
 * @param	string[optional] $separator		The separator between the words
 */
function toCamelCase($inputValue, $firstCharUpperCase = false, $separator = '_') {
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
	    if ($i != 0) $word = ucfirst($word);
	    else if ($firstCharUpperCase) $word = ucfirst($word);

	    // append the word to the return value
	    $returnValue .= $word;
	}
    }

    // return resulting string
    return $returnValue;
}

?>
