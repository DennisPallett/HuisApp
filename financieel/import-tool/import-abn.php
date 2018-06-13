<?php
//require __DIR__ . '/vendor/autoload.php';
require 'model/BankStatement.php';

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
	INSERT INTO entry (statement_id, reference, booking_date, value_date, amount, description, 
	other_party_name, other_party_address, other_party_account, remittance_info,
	is_card_payment, is_cash_withdrawal, is_shop_sale, start_balance_amount, end_balance_amount) 
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
	logMessage("Processing file " . $file . " ...");

	$statement = new BankStatement();
	$statement->processFile($importDirectory . $file);

	
	logMessage("File processed");

	logMessage("Inserting statement into database...");

	$conn->beginTransaction();

	$insertStatementQuery->bindValue(1, $statement->id);
	$insertStatementQuery->bindValue(2, $statement->creationDateTime);
	$insertStatementQuery->bindValue(3, $statement->startBalance->date);
	$insertStatementQuery->bindValue(4, $statement->startBalance->amount);
	$insertStatementQuery->bindValue(5, $statement->endBalance->date);
	$insertStatementQuery->bindValue(6, $statement->endBalance->amount);

	$ret = $insertStatementQuery->execute();

	if (!$ret) {
		$conn->rollback();

		$errorInfo = $insertStatementQuery->errorInfo();
		$errorCode = $errorInfo['0'];

		if ($errorCode == '23505') {
			logMessage("Statement already present in database");
			$duplicateCount++;
			continue;
		} else {
			logMessage("ERROR: " . $errorInfo[2]);
			die();
		}
			
	} else {
		$importStatementCount++;
		logMessage("Statement inserted");
	}

	logMessage("Inserting entries...");
	$startBalanceAmount = $statement->startBalance->amount;
	foreach($statement->entries as $entry) {
		$endBalanceAmount = $startBalanceAmount + $entry->amount;

		$insertEntryQuery->bindValue(1, $statement->id);
		$insertEntryQuery->bindValue(2, $entry->id);
		$insertEntryQuery->bindValue(3, $entry->bookingDate);
		$insertEntryQuery->bindValue(4, $entry->valueDate);
		$insertEntryQuery->bindValue(5, $entry->amount);
		$insertEntryQuery->bindValue(6, $entry->description);

		if ($entry->otherParty != null) {
			$insertEntryQuery->bindValue(7, $entry->otherParty->name);
			$insertEntryQuery->bindValue(8, $entry->otherParty->address);
			$insertEntryQuery->bindValue(9, $entry->otherParty->account);
		} else {
			$insertEntryQuery->bindValue(7, null);
			$insertEntryQuery->bindValue(8, null);
			$insertEntryQuery->bindValue(9, null);
		}

		$insertEntryQuery->bindValue(10, $entry->remittanceInfo);

		$insertEntryQuery->bindValue(11, (int) $entry->isCardPayment);
		$insertEntryQuery->bindValue(12, (int) $entry->isCashWithdrawal);
		$insertEntryQuery->bindValue(13, (int) $entry->isShopSale);
		$insertEntryQuery->bindValue(14, $startBalanceAmount);
		$insertEntryQuery->bindValue(15, $endBalanceAmount);

		$ret = $insertEntryQuery->execute();

		if (!$ret) {
			$conn->rollback();

			$errorInfo = $insertEntryQuery->errorInfo();
			$errorCode = $errorInfo['0'];

			logMessage("ERROR: " . $errorInfo[2]);
			die();
			
		} else {
			$importEntryCount++;
		}

		$startBalanceAmount = $endBalanceAmount;
	}
	logMessage("Entries inserted");

	$conn->commit();
}

logMessage("Summary:");

logMessage("Aantal statements geimporteerd: " . $importStatementCount);
logMessage("Aantal entries geimporteerd: " . $importEntryCount);
logMessage("Aantal oude statements gevonden: " . $duplicateCount);

logMessage("Klaar!");

function logMessage($message) {
	echo "- " . $message . "\n";
}