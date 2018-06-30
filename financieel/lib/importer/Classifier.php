<?php
require 'IImportLogger.php';
require 'IRule.php';
require 'model/BankStatement.php';

class Classifier {
	public $classifiedCount;

	public $unclassifiedCount;

	private $_connection;

	private $_loggers = array();

	private $_rules = array();

	private $_ruleObjects = array();

	private $_entries = array();

	private $_updateQuery;

	public function __construct($connection) {
		$this->_connection = $connection;

		$this->_updateQuery = $connection->prepare("UPDATE entry SET category = ? WHERE id = ?");
	}

	public function addLogger(IImportLogger $logger) {
		$this->_loggers[] = $logger;
	}

	public function classify() {
		$this->loadRules();

		$this->loadEntries();

		return $this->classifyEntries();
	}

	private function classifyEntries () {
		$this->logMessage("Classifying entries...");
		$this->classifiedCount = 0;
		$this->unclassifiedCount = 0;

		$results = array();
		foreach($this->_entries as $entry) {
			$category = $this->classifyEntry($entry);

			if ($category != null) {
				$results[$entry['id']] = $category;
			}
		}

		$this->logMessage("Finished classification");
		return $results;
	}

	private function classifyEntry ($entry) {
		$category = null;

		foreach($this->_ruleObjects as $ruleObj) {
			$category = $ruleObj->classifyEntry($entry);

			// found a category -> stop classifying
			if ($category != null)
				break;
		}

		// no category found -> exit function
		if ($category == null)
		{
			$this->unclassifiedCount++;
			return;
		}

		// update entry with category
		$this->logMessage("Entry #" . $entry['id'] . " classified as: " . $category);

		// update in database
		$this->_updateQuery->bindValue(1, $category);
		$this->_updateQuery->bindValue(2, $entry['id']);

		$ret = false;
		try {
			$ret = $this->_updateQuery->execute();
		} catch (Exception $e) {
		}

		if (!$ret) {
			$errorInfo = $this->_insertEntryQuery->errorInfo();
			$errorCode = $errorInfo['0'];
			$errorMessage = $errorInfo['2'];

			throw new Exception($errorMessage . ' (' . $errorCode . ')');
		}

		$this->classifiedCount++;

		return $category;
	}

	private function loadEntries () {
		$this->logMessage("Loading unclassified entries...");

		$this->_entries = $this->_connection->query("SELECT * FROM entry WHERE category IS NULL");
		
		$this->logMessage("Loaded entries");
	}

	private function loadRules () {
		$this->logMessage("Loading classification rules...");

		foreach (glob(dirname(__FILE__) . "/rules/*.php") as $filename) {
			include $filename;
		}

		$classes = get_declared_classes();
		foreach($classes as $klass) {
			$reflect = new ReflectionClass($klass);
			if($reflect->implementsInterface('IRule')) 
			$this->_rules[] = $klass;
		}

		$this->logMessage("Found " . count($this->_rules) . " rules");

		if (count($this->_rules) == 0)
			throw new Exception("No classification rules available");

		foreach($this->_rules as $rule) {
			$this->_ruleObjects[] = new $rule();
		}
	}

	private function logMessage($message) {
		foreach($this->_loggers as $logger) {
			$logger->logMessage($message);
		}
	}
}

class DuplicateStatementException extends Exception {
}