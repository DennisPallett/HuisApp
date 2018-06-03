<?php
//require __DIR__ . '/vendor/autoload.php';
require 'BankStatement.php';

$importDirectory = ".\import\\";

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

logMessage("Started ABN Financieel import tool");

$config = parse_ini_file(".\config.ini", true);

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

$insertStatementQuery = $conn->prepare("
	INSERT INTO statement (id, creation_datetime, start_balance_date, start_balance_amount, end_balance_date, end_balance_amount) 
	VALUES (?, ?, ?, ?, ?, ?)
");

$insertEntryQuery = $conn->prepare("
	INSERT INTO entry (statement_id, reference, booking_date, value_date, amount, description) 
	VALUES (?, ?, ?, ?, ?, ?)
");

logMessage("Scanning import directory '" . $importDirectory . " for CSV files");
$files = scandir($importDirectory);

$files = array_filter($files, function ($file) { 
	if (substr($file, 0, 1) != '.') return $file;
});

logMessage("Found " . count($files) . " files in import directory");

$importStatementCount = 0;
$importEntryCount = 0;
$duplicateCount = 0;
foreach($files as $file) {	
	// convert XML file into object
	logMessage("Processing file " . $file);

	$statement = new BankStatement();
	$statement->processFile($importDirectory . $file);

	//print_r($statement);
	//die();

	$conn->beginTransaction();

	$insertStatementQuery->bindParam(1, $statement->id);
	$insertStatementQuery->bindParam(2, $statement->creationDateTime);
	$insertStatementQuery->bindParam(3, $statement->startBalance->date);
	$insertStatementQuery->bindParam(4, $statement->startBalance->amount);
	$insertStatementQuery->bindParam(5, $statement->endBalance->date);
	$insertStatementQuery->bindParam(6, $statement->endBalance->amount);

	$ret = $insertStatementQuery->execute();
	$importStatementCount++;

	foreach($statement->entries as $entry) {
		$insertEntryQuery->bindParam(1, $statement->id);
		$insertEntryQuery->bindParam(2, $entry->id);
		$insertEntryQuery->bindParam(3, $entry->bookingDate);
		$insertEntryQuery->bindParam(4, $entry->valueDate);
		$insertEntryQuery->bindParam(5, $entry->amount);
		$insertEntryQuery->bindParam(6, $entry->description);

		$ret = $insertEntryQuery->execute();

		if (!$ret) {
			$errorInfo = $insertEntryQuery->errorInfo();
			$errorCode = $errorInfo['0'];

			//if ($errorCode == '23505') {
			//	$duplicateCount++;
			//} else {
				logMessage("ERROR: " . $errorInfo[2]);
				die();
			//}
			
		} else {
			$importCount++;
		}

		$importEntryCount++;
	}

	$conn->commit();

	logMessage("File processed");

	//die();

}

logMessage("Aantal statements geimporteerd: " . $importStatementCount);
logMessage("Aantal entries geimporteerd: " . $importEntryCount);
//logMessage("Aantal oude records gevonden: " . $duplicateCount);

logMessage("Klaar!");

function logMessage($message) {
	echo "- " . $message . "\n";
}