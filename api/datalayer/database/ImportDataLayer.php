<?php
namespace datalayer\database;

class ImportDataLayer implements \IImportDataLayer {
	private $db;

	private $datalayer;

	private $insertQuery;

	public function __construct(Datalayer $datalayer, \PDO $db) {
		$this->db = $db;
		$this->datalayer = $datalayer;

		$this->insertQuery = $db->prepare("
			INSERT INTO " . $this->datalayer->quoteIdentifier("temperatuur") . " (
			" . $this->datalayer->quoteIdentifier("timestamp") . ", 
			" . $this->datalayer->quoteIdentifier("temperature_indoor") . ", 
			" . $this->datalayer->quoteIdentifier("humidity_indoor") . ", 
			" . $this->datalayer->quoteIdentifier("dew_indoor") . ", 
			" . $this->datalayer->quoteIdentifier("temperature_1") . ", 
			" . $this->datalayer->quoteIdentifier("humidity_1") . ", 
			" . $this->datalayer->quoteIdentifier("dew_1") . ", 
			" . $this->datalayer->quoteIdentifier("temperature_2") . ", 
			" . $this->datalayer->quoteIdentifier("humidity_2") . ", 
			" . $this->datalayer->quoteIdentifier("dew_2") . ", 
			" . $this->datalayer->quoteIdentifier("temperature_3") . ",  
			" . $this->datalayer->quoteIdentifier("humidity_3") . ", 
			" . $this->datalayer->quoteIdentifier("dew_3") . ", 
			" . $this->datalayer->quoteIdentifier("temperature_4") . ", 
			" . $this->datalayer->quoteIdentifier("humidity_4") . ", 
			" . $this->datalayer->quoteIdentifier("dew_4") . ", 
			" . $this->datalayer->quoteIdentifier("temperature_5") . ",  
			" . $this->datalayer->quoteIdentifier("humidity_5") . ", 
			" . $this->datalayer->quoteIdentifier("dew_5") . ", 
			" . $this->datalayer->quoteIdentifier("temperature_6") . ",  
			" . $this->datalayer->quoteIdentifier("humidity_6") . ", 
			" . $this->datalayer->quoteIdentifier("dew_6") . ", 
			" . $this->datalayer->quoteIdentifier("temperature_7") . ",  
			" . $this->datalayer->quoteIdentifier("humidity_7") . ", 
			" . $this->datalayer->quoteIdentifier("dew_7") . ", 
			" . $this->datalayer->quoteIdentifier("temperature_8") . ", 
			" . $this->datalayer->quoteIdentifier("humidity_8") . ", 
			" . $this->datalayer->quoteIdentifier("dew_8") . "
			) VALUES (?, ?, ?, ?,	?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		");
	}

	public function saveTemperatuurEntry (\TemperatuurEntry $entry) {
		$this->insertQuery->bindValue(1, $entry->Timestamp);
		$this->insertQuery->bindValue(2, $entry->TI);
		$this->insertQuery->bindValue(3, $entry->RHI);
		$this->insertQuery->bindValue(4, $entry->DEWI);
		$this->insertQuery->bindValue(5, $entry->T1);
		$this->insertQuery->bindValue(6, $entry->RH1);
		$this->insertQuery->bindValue(7, $entry->DEW1);
		$this->insertQuery->bindValue(8, $entry->T2);
		$this->insertQuery->bindValue(9, $entry->RH2);
		$this->insertQuery->bindValue(10, $entry->DEW2);
		$this->insertQuery->bindValue(11, $entry->T3);
		$this->insertQuery->bindValue(12, $entry->RH3);
		$this->insertQuery->bindValue(13, $entry->DEW3);
		$this->insertQuery->bindValue(14, $entry->T4);
		$this->insertQuery->bindValue(15, $entry->RH4);
		$this->insertQuery->bindValue(16, $entry->DEW4);
		$this->insertQuery->bindValue(17, $entry->T5);
		$this->insertQuery->bindValue(18, $entry->RH5);
		$this->insertQuery->bindValue(19, $entry->DEW5);
		$this->insertQuery->bindValue(20, $entry->T6);
		$this->insertQuery->bindValue(21, $entry->RH6);
		$this->insertQuery->bindValue(22, $entry->DEW6);
		$this->insertQuery->bindValue(23, $entry->T7);
		$this->insertQuery->bindValue(24, $entry->RH7);
		$this->insertQuery->bindValue(25, $entry->DEW7);
		$this->insertQuery->bindValue(26, $entry->T8);
		$this->insertQuery->bindValue(27, $entry->RH8);
		$this->insertQuery->bindValue(28, $entry->DEW8);

		$ret = false;
		try {
			$ret = $this->insertQuery->execute();
		} catch (\Exception $e) {
		}

		if (!$ret) {
			$errorInfo = $this->insertQuery->errorInfo();
			$errorCode = $errorInfo['0'];
			$errorMessage = $errorInfo['2'];

			if ($errorCode == '23505') {
				throw new \DuplicateTemperatuurEntryException();
			} else {
				throw new \Exception($errorMessage . ' (' . $errorCode . ')');
			}
		}
		
	}
}