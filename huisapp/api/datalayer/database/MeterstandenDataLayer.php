<?php
namespace datalayer\database;

class MeterstandenDataLayer implements \datalayer\IMeterstandenDataLayer {
	private $db;

	private $datalayer;

	public function __construct(Datalayer $datalayer, \PDO $db) {
		$this->db = $db;
		$this->datalayer = $datalayer;
	}

	function getMeterstand($opnameDatum) {
		$sql = "SELECT * FROM " . $this->datalayer->quoteIdentifier("meterstanden") .
			" WHERE opname_datum = :opnameDatum";

		$statement = $this->db->prepare($sql);

		$ret = $statement->execute(array('opnameDatum' => $opnameDatum));

		$row = $statement->fetch();
		if ($row == false)
			return null;

		$meterstand = new \business\model\Meterstand();
		$meterstand->opnameDatum = $row['opname_datum'];
		$meterstand->water = $row['stand_water'];
		$meterstand->gas = $row['stand_gas'];
		$meterstand->elektraE1 = $row['stand_elektra_e1'];
		$meterstand->elektraE2 = $row['stand_elektra_e2'];

		return $meterstand;
	}

	function updateMeterstand($opnameDatum, \business\model\Meterstand $meterstand) {
		$sql = "UPDATE " . $this->datalayer->quoteIdentifier("meterstanden") . " SET " .
			$this->datalayer->quoteIdentifier("opname_datum") . " = :opnameDatum, " .
			$this->datalayer->quoteIdentifier("stand_water") . " = :water, " .
			$this->datalayer->quoteIdentifier("stand_gas") . " = :gas, " .
			$this->datalayer->quoteIdentifier("stand_elektra_e1") . " = :elektraE1, " .
			$this->datalayer->quoteIdentifier("stand_elektra_e2") . " = :elektraE2" .
			" WHERE opname_datum = :opnameDatum";

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

	public function deleteMeterstand($opnameDatum) {
		$sql = "DELETE FROM " . $this->datalayer->quoteIdentifier("meterstanden") .
			" WHERE opname_datum = :opnameDatum";

		$statement = $this->db->prepare($sql);

		$ret = $statement->execute(array('opnameDatum' => $opnameDatum));
		return $ret;
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

	public function getMeterstanden ($sortBy, $sortOrder) {
		$bindParams = array();

		$sql = "SELECT * FROM meterstanden";
		$sql .= " ORDER BY " . $sortBy . ' ' . $sortOrder;
		
		$statement = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
		$statement->execute($bindParams);

		return $statement->fetchAll();
	}
}