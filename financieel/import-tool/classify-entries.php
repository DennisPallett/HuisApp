<?php
require 'IRule.php';

logMessage("Started Entry Classifier tool");

$config = parse_ini_file(".\config.ini", true);

$rules = $config['classifier']['rules'];

if (empty($rules)) {
	die("ERROR: missing classifier rules in config.");
}

if (!is_array($rules)) $rules = array($rules);

spl_autoload_register(function ($class) {
	if (file_exists('rules/' . $class . '.php'))
		include('rules/' . $class . '.php');
});

// verify rules are valid
$ruleObjects = array();
foreach($rules as $rule) {
	if (!class_exists($rule)) {
		die("ERROR: unknown rule " . $rule . " specified in config.");
	}

	$ruleObj = new $rule();
	if (!($ruleObj instanceof IRule)) {
		die("ERROR: rule " . $rule . " does not implement IRule interface.");
	}

	$ruleObjects[] = $ruleObj;
}

// connect to the postgresql database
$conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s", 
    $config['db']['host'], 
    $config['db']['port'], 
    $config['db']['database'], 
    $config['db']['user'], 
    $config['db']['password']);

logMessage("Setting up database connection to '" . $config['db']['database'] . "'...");

$conn = new \PDO($conStr);

logMessage("Database connection established");

logMessage("Loading unclassified entries...");

$entries = $conn->query("SELECT * FROM entry WHERE category IS NULL");

logMessage("Loaded entries");

$updateQuery = $conn->prepare("UPDATE entry SET category = ? WHERE id = ?");

logMessage("Classifying entries...");
$classifiedCount = 0;
$unclassifiedCount = 0;
foreach($entries as $entry) {
	$category = null;
	foreach($ruleObjects as $ruleObj) {
		$category = $ruleObj->classifyEntry($entry);

		// found a category -> stop classifying
		if ($category != null)
			break;
	}

	if ($category != null) {
		logMessage("Entry #" . $entry['id'] . " classified as: " . $category);

		// update in database
		$updateQuery->bindValue(1, $category);
		$updateQuery->bindValue(2, $entry['id']);

		$ret = $updateQuery->execute();

		if (!$ret) {
			$errorInfo = $updateQuery->errorInfo();
			$errorCode = $errorInfo['0'];

			logMessage("ERROR: " . $errorInfo[2]);
			die();
			
		} else {
			$classifiedCount++;
		}
	} else {
		$unclassifiedCount++;
	}
}

logMessage("Summary:");

logMessage("Aantal entries classified: " . $classifiedCount);
logMessage("Aantal entries unclassified: " . $unclassifiedCount);

logMessage("Klaar!");

function logMessage($message) {
	echo "- " . $message . "\n";
}