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
    generateClassesForTable($database, $table);
}

function generateClassesForTable($database, $table) {
    echo "Generating classes for $table... ";

    // get it's columns
    $records = $database->getRecords("SHOW COLUMNS FROM $table");

    // prepare vars
    $columns = array();
    $numberOfPrimaryKeys = 0;
    foreach ($records as $record) {
	$column = array();
	// make fieldname camel cased
	$column['fieldName'] = toCamelCase($record['Field']);
	$column['originalFieldName'] = $record['Field'];
	$column['type'] = getTypePerLanguage($record['Type']);
	$column['isNull'] = $record['Null'] === 'YES' ? true : false;
	$column['isPrimaryKey'] = $record['Key'] === "PRI" ? true : false;
	$column['isDefaultValue'] = $record['Default'];
	$column['extra'] = $record['Extra'];

	if($column['isPrimaryKey']) {
	    $columns['primaryKey'] = $column;
	    $numberOfPrimaryKeys++;
	}
	else $columns[] = $column;
    }

    // generate the classes for every item in the directory
    // but only if the table has 1 primary key (we want to exclude junction tables)
    if($numberOfPrimaryKeys == 1) generateEveryFileInDir('', $table, $columns);

    echo "DONE \n";
}

function generateEveryFileInDir($directory, $table, $columns) {
    // only run this function if the given directory is in fact a directory
    if (is_dir(TEMPLATE_DIRECTORY . '/' . $directory)) {
	// get the directory content
	$directoryContent = scandir(TEMPLATE_DIRECTORY . '/' . $directory);

	$directory = (empty($directory) ? '' : $directory . '/');
	// for ever item in the directory...
	foreach ($directoryContent as $item) {
	    // ignore the . and .. references
	    if ($item === '.' || $item === '..') {
		// do nothing
	    } else {
		// test if it's a directory on it's own
		if (is_dir(TEMPLATE_DIRECTORY . '/' . $directory . $item)) {
		    // generate directory if not exists
		    if (!file_exists('generated_classes/' . $directory)) mkdir('generated_classes/' . $directory);

		    // generate the files in that directory
		    generateEveryFileInDir($directory . $item, $table, $columns);
		} else {
		    // create output directory if not exists
		    if (!file_exists('generated_classes/' . $directory)) mkdir('generated_classes/' . $directory);
		    // generate the files in this directory
		    generateFile(TEMPLATE_DIRECTORY . '/' . $directory, $item, 'generated_classes/' . $directory . str_replace('%Classname%',  toCamelCase($table, true), $item), $table, $columns);
		}
	    }
	}
    }
}

function generateFile($templateDir, $templateFileName, $generatedFile, $table, $columns) {
    // prepare template compiler
    $templateCompiler = new Smarty();
    $templateCompiler->setTemplateDir($templateDir);
    $templateCompiler->setCompileDir('compiled_templates/');
    $templateCompiler->setConfigDir('configs/');
    $templateCompiler->setCacheDir('cache/');

    // assign vars
    $templateCompiler->assign('className', toCamelCase($table, true));
    $templateCompiler->assign('tableName', $table);
    $templateCompiler->assign('fields', $columns);
    $templateCompiler->assign('authorName', AUTHOR_NAME);
    $templateCompiler->assign('authorEmail', AUTHOR_EMAIL);

    // fetch file content'
    $fileContent = $templateCompiler->fetch($templateFileName);

    // save to new file
    $file = fopen($generatedFile, 'w') or die("Couldn't create file: " . toCamelCase($table));
    fwrite($file, $fileContent);
    fclose($file);
}

/**
 * returns the extension for a filename.
 *
 * @return	string		    The extension.
 * @param	string $filename    The full path of the file.
 */
function getExtension($filename) {
    // init var
    $filename = (string) $filename;

    // get extension
    $parts = (array) explode('.', $filename);

    // count the parts
    $count = count($parts);

    // return the last part
    if ($count != 0)
	return $parts[$count - 1];

    // no extension
    return '';
}

function getTypePerLanguage ($SQLtype) {
    // init vars
    $types = array();

    // Boolean
    if(preg_match('/bool/i', $SQLtype) || preg_match('/boolean/i', $SQLtype)) {
	$types = array('php' => 'boolean', 'as' => 'Boolean');
    }

    // Date
    if(preg_match('/date/i', $SQLtype)) {
	$types = array('php' => 'date', 'as' => 'Date');
    }

    // timestamp
    if(preg_match('/timestamp/i', $SQLtype)) {
	$types = array('php' => 'int', 'as' => 'Number');
    }

    // float
    if(preg_match('/float/i', $SQLtype)) {
	$types = array('php' => 'float', 'as' => 'Number');
    }

    // decimal
    if(preg_match('/decimal/i', $SQLtype)) {
	$types = array('php' => 'float', 'as' => 'Number');
    }

    // Integer
    if(preg_match('/int\(\d+\)/i', $SQLtype)) {
	$types = array('php' => 'int', 'as' => 'Number');
    }

    // String
    if(preg_match('/varchar\(\d+\)/i', $SQLtype)) {
	$types = array('php' => 'string', 'as' => 'String');
    }

    // return types
    return $types;
}

/**
 * Converts a string to camelcasing.
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
	if ($word != '') {
	    // make word lowercase
	    $word = strtolower($word);

	    // When it's not the first word and we shouldn't use lowercase for the first word convert first letter to uppercase
	    if ($i != 0)
		$word = ucfirst($word);
	    else if ($firstCharUpperCase)
		$word = ucfirst($word);

	    // append the word to the return value
	    $returnValue .= $word;
	}
    }

    // return resulting string
    return $returnValue;
}

?>
