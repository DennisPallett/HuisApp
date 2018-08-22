<?php
namespace datalayer\mysql;

class StatementsDataLayer extends \datalayer\database\StatementsDataLayer {
	private $db;

	public function __construct($db) {
		parent::__construct($db);
		$this->db = $db;
	}

	
}