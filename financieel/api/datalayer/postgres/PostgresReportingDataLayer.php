<?php

class PostgresReportingDataLayer {
	
	private $db;

	public function __construct($db) {
		$this->db = $db;
	}

	public function getBalances () {
		return $records = $this->db->query("
			SELECT 
				start_balance_date, start_balance_amount,
				end_balance_date, end_balance_amount
			FROM \"statement\"
			ORDER BY start_balance_date ASC
		");
	}
}