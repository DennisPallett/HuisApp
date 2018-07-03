<?php
require __DIR__ . '/vendor/autoload.php';
use League\Csv\Reader;

$importDirectory = ".\import\\";

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

logMessage("Started KlimaLog import tool");

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

$insertQuery = $conn->prepare("
	INSERT INTO temperatuur (
	timestamp, 
	temperature_indoor, humidity_indoor, dew_indoor,
	temperature_1, humidity_1, dew_1,
	temperature_2, humidity_2, dew_2,
	temperature_3, humidity_3, dew_3,
	temperature_4, humidity_4, dew_4,
	temperature_5, humidity_5, dew_5,
	temperature_6, humidity_6, dew_6,
	temperature_7, humidity_7, dew_7,
	temperature_8, humidity_8, dew_8
	) 
	VALUES (?, ?, ?, ?,	?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

logMessage("Scanning import directory '" . $importDirectory . " for CSV files");
$files = scandir($importDirectory);

$importCount = 0;
$duplicateCount = 0;
foreach($files as $file) {
	if (substr($file, 0, 1) == '.') continue;
	
	logMessage("Found file " . $file . " in import directory");
	logMessage("Loading CSV file...");

	$csv = Reader::createFromPath($importDirectory . $file, 'r');
	$csv->setHeaderOffset(0);
	$csv->setDelimiter(';');

	$records = $csv->getRecords();

	logMessage("CSV file loaded. Importing records...");

	foreach($records as $record) {
		// vertaal --- naar NULLs
		$record = array_map(function ($value) {
			return ($value == '---') ? null : $value;
		}, $record);
		

		$insertQuery->bindParam(1, $record['Timestamp']);
		$insertQuery->bindParam(2, $record['TI']);
		$insertQuery->bindParam(3, $record['RHI']);
		$insertQuery->bindParam(4, $record['DEWI']);
		$insertQuery->bindParam(5, $record['T1']);
		$insertQuery->bindParam(6, $record['RH1']);
		$insertQuery->bindParam(7, $record['DEW1']);
		$insertQuery->bindParam(8, $record['T2']);
		$insertQuery->bindParam(9, $record['RH2']);
		$insertQuery->bindParam(10, $record['DEW2']);
		$insertQuery->bindParam(11, $record['T3']);
		$insertQuery->bindParam(12, $record['RH3']);
		$insertQuery->bindParam(13, $record['DEW3']);
		$insertQuery->bindParam(14, $record['T4']);
		$insertQuery->bindParam(15, $record['RH4']);
		$insertQuery->bindParam(16, $record['DEW4']);
		$insertQuery->bindParam(17, $record['T5']);
		$insertQuery->bindParam(18, $record['RH5']);
		$insertQuery->bindParam(19, $record['DEW5']);
		$insertQuery->bindParam(20, $record['T6']);
		$insertQuery->bindParam(21, $record['RH6']);
		$insertQuery->bindParam(22, $record['DEW6']);
		$insertQuery->bindParam(23, $record['T7']);
		$insertQuery->bindParam(24, $record['RH7']);
		$insertQuery->bindParam(25, $record['DEW7']);
		$insertQuery->bindParam(26, $record['T8']);
		$insertQuery->bindParam(27, $record['RH8']);
		$insertQuery->bindParam(28, $record['DEW8']);
		
		$ret = $insertQuery->execute();

		if (!$ret) {
			$errorInfo = $insertQuery->errorInfo();
			$errorCode = $errorInfo['0'];

			if ($errorCode == '23505') {
				$duplicateCount++;
			} else {
				logMessage("ERROR: " . $errorInfo[2]);
				die();
			}
			
		} else {
			$importCount++;
		}
	}
}

logMessage("Aantal records geimporteerd: " . $importCount);
logMessage("Aantal oude records gevonden: " . $duplicateCount);

logMessage("Klaar!");

function logMessage($message) {
	echo "- " . $message . "\n";
}