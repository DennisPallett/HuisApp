<?php
require_once 'IImportLogger.php';
require_once 'IImportDataLayer.php';
require_once 'model/TemperatuurEntry.php';

use League\Csv\Reader;

class Importer {
	public $importCount = 0;

	public $duplicateCount = 0;

	private $_loggers = array();

	private $_dataLayer;

	public function __construct(IImportDataLayer $dataLayer) {
		$this->_dataLayer = $dataLayer;
	}

	public function addLogger(IImportLogger $logger) {
		$this->_loggers[] = $logger;
	}

	public function importFile($file) {
		$csv = Reader::createFromPath($file, 'r');
		$csv->setHeaderOffset(0);
		$csv->setDelimiter(';');

		$records = $csv->getRecords();

		foreach($records as $record) {
			$this->processRecord($record);
		}
	}

	private function processRecord($record) {
		// vertaal --- naar NULLs
		$record = array_map(function ($value) {
			return ($value == '---') ? null : $value;
		}, $record);

		// map record naar object
		$entry = new TemperatuurEntry();
		foreach($record as $key => $value) {
			$entry->$key = $value;
		}

		// opslaan in datalayer
		try {
			$this->_dataLayer->saveTemperatuurEntry($entry);
			$this->importCount++;
		} catch (\DuplicateTemperatuurEntryException $e) {
			$this->duplicateCount++;
		}
	}

}

class DuplicateTemperatuurEntryException extends Exception {
}