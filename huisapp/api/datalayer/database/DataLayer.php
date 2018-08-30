<?php
namespace datalayer\database;

abstract class DataLayer implements \datalayer\IDataLayer {
	private $db;

	protected function __construct($db) {
		$this->db = $db;
	}

	public abstract function quoteIdentifier($identifier);

}