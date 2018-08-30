<?php
namespace datalayer\database;

class MeterstandenDataLayer implements \datalayer\IMeterstandenDataLayer {
	private $db;

	private $datalayer;

	public function __construct(Datalayer $datalayer, \PDO $db) {
		$this->db = $db;
		$this->datalayer = $datalayer;
	}

	public function insertMeterstand(\business\model\Meterstand $meterstand) {
		$sql = "INSERT INTO " . $this->datalayer->quoteIdentifier("meterstanden") . " (
		" . $this->datalayer->quoteIdentifier("opname_datum") . ", 
		" . $this->datalayer->quoteIdentifier("stand_water") . ", 
		" . $this->datalayer->quoteIdentifier("stand_gas") . ", 
		" . $this->datalayer->quoteIdentifier("stand_elektra_e1") . ", 
		" . $this->datalayer->quoteIdentifier("stand_elektra_e2") . "
		) VALUES (:opnameDatum, :water, :gas, :elektraE1, :elektraE2)";

		$statement = $this->db->prepare($sql);

		$ret = false;
		try {
			$ret = $statement->execute((array)$meterstand);
		} catch (\Exception $e) {
		}

		if (!$ret) {
			$errorInfo = $statement->errorInfo();
			$errorCode = $errorInfo['0'];
			$errorMessage = $errorInfo['2'];

			if ($errorCode == '23505') {
				throw new \datalayer\DuplicateMeterstandException();
			} else {
				throw new Exception($errorMessage . ' (' . $errorCode . ')');
			}
		}

		return true;
	}

	public function getMeterstanden ($year, $month, $sortBy, $sortOrder) {
		$bindParams = array();

		$sql = "SELECT 
			*
		FROM meterstanden
		WHERE 1=1
		";

		if (!empty($month) && is_numeric($month)) {
			//$sql .= " AND date_part('month', start_balance_date) = :month";
			//$bindParams[':month'] = $month;
		}

		if (!empty($year) && is_numeric($year)) {
			//$sql .= " AND date_part('year', start_balance_date) = :year";
			//$bindParams[':year'] = $year;
		}

		$sql .= " ORDER BY " . $sortBy . ' ' . $sortOrder;
		
		$statement = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
		$statement->execute($bindParams);

		return $statement->fetchAll();
	}

	public function deleteTransactions($month, $year) {
		$bindParams = array();

		$sql = "DELETE FROM entry WHERE statement_id IN (
			SELECT id FROM statement WHERE
				date_part('month', start_balance_date) = :month
				AND date_part('year', start_balance_date) = :year
		)";

		$bindParams[':month'] = $month;
		$bindParams[':year'] = $year;

		$statement = $this->db->prepare($sql);
		$statement->execute($bindParams);
	}
}