<?php
require 'model/BankStatement.php';

class Importer {
	public $importEntryCount = 0;

	private $_connection;

	private $_insertStatementQuery;

	private $__insertEntryQuery;

	private $_loggers = array();

	public function __construct($connection) {
		$this->_connection = $connection;

		$this->_insertStatementQuery = $connection->prepare("
			INSERT INTO statement (id, creation_datetime, start_balance_date, start_balance_amount, end_balance_date, end_balance_amount) 
			VALUES (?, ?, ?, ?, ?, ?)
		");

		$this->_insertEntryQuery = $connection->prepare("
			INSERT INTO entry (statement_id, reference, booking_date, value_date, amount, description, 
			other_party_name, other_party_address, other_party_account, remittance_info,
			is_card_payment, is_cash_withdrawal, is_shop_sale, start_balance_amount, end_balance_amount) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		");
	}

	public function addLogger(IImportLogger $logger) {
		$this->_loggers[] = $logger;
	}

	public function importFile($file) {
		// convert XML file into object
		$this->logMessage("Processing file " . $file . " ...");

		$statement = new BankStatement();
		$statement->processFile($file);

		$this->logMessage("File processed");

		$this->logMessage("Inserting statement into database...");

		$this->_connection->beginTransaction();

		try {
			$this->insertStatement($statement);
			$this->insertEntries($statement);
		} catch (Exception $e) {
			$this->_connection->rollback();
			throw $e;
		}
		
		$this->_connection->commit();
	}

	private function insertStatement ($statement) {
		$this->_insertStatementQuery->bindValue(1, $statement->id);
		$this->_insertStatementQuery->bindValue(2, $statement->creationDateTime);
		$this->_insertStatementQuery->bindValue(3, $statement->startBalance->date);
		$this->_insertStatementQuery->bindValue(4, $statement->startBalance->amount);
		$this->_insertStatementQuery->bindValue(5, $statement->endBalance->date);
		$this->_insertStatementQuery->bindValue(6, $statement->endBalance->amount);

		$ret = false;
		try {
			$ret = $this->_insertStatementQuery->execute();
		} catch (Exception $e) {
		}

		if (!$ret) {
			$errorInfo = $this->_insertStatementQuery->errorInfo();
			$errorCode = $errorInfo['0'];
			$errorMessage = $errorInfo['2'];

			if ($errorCode == '23505') {
				throw new DuplicateStatementException();
			} else {
				throw new Exception($errorMessage . ' (' . $errorCode . ')');
			}
		}

		$this->logMessage("Statement inserted");
	}

	private function insertEntries ($statement) {
		$this->logMessage("Inserting entries...");

		$startBalanceAmount = $statement->startBalance->amount;
		foreach($statement->entries as $entry) {
			$endBalanceAmount = $startBalanceAmount + $entry->amount;

			$this->_insertEntryQuery->bindValue(1, $statement->id);
			$this->_insertEntryQuery->bindValue(2, $entry->id);
			$this->_insertEntryQuery->bindValue(3, $entry->bookingDate);
			$this->_insertEntryQuery->bindValue(4, $entry->valueDate);
			$this->_insertEntryQuery->bindValue(5, $entry->amount);
			$this->_insertEntryQuery->bindValue(6, $entry->description);

			if ($entry->otherParty != null) {
				$this->_insertEntryQuery->bindValue(7, $entry->otherParty->name);
				$this->_insertEntryQuery->bindValue(8, $entry->otherParty->address);
				$this->_insertEntryQuery->bindValue(9, $entry->otherParty->account);
			} else {
				$this->_insertEntryQuery->bindValue(7, null);
				$this->_insertEntryQuery->bindValue(8, null);
				$this->_insertEntryQuery->bindValue(9, null);
			}

			$this->_insertEntryQuery->bindValue(10, $entry->remittanceInfo);

			$this->_insertEntryQuery->bindValue(11, (int) $entry->isCardPayment);
			$this->_insertEntryQuery->bindValue(12, (int) $entry->isCashWithdrawal);
			$this->_insertEntryQuery->bindValue(13, (int) $entry->isShopSale);
			$this->_insertEntryQuery->bindValue(14, $startBalanceAmount);
			$this->_insertEntryQuery->bindValue(15, $endBalanceAmount);

			$ret = false;
			try {
				$ret = $this->_insertEntryQuery->execute();
			} catch (Exception $e) {
			}

			if (!$ret) {
				$errorInfo = $this->_insertEntryQuery->errorInfo();
				$errorCode = $errorInfo['0'];
				$errorMessage = $errorInfo['2'];

				throw new Exception($errorMessage . ' (' . $errorCode . ')');
			}

			$startBalanceAmount = $endBalanceAmount;

			$this->importEntryCount++;
		}

		$this->logMessage("Entries inserted");
	}

	private function logMessage($message) {
		foreach($this->_loggers as $logger) {
			$logger->logMessage($message);
		}
	}
}

class DuplicateStatementException extends Exception {
}

interface IImportLogger {
	public function logMessage($message);
}