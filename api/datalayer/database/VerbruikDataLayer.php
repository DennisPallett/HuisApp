<?php
namespace datalayer\database;

class VerbruikDataLayer implements \datalayer\IVerbruikDataLayer {
	private $db;

	private $datalayer;

	public function __construct(Datalayer $datalayer, \PDO $db) {
		$this->db = $db;
		$this->datalayer = $datalayer;
	}

	public function getPerMaand () {
		$sql = "SELECT
			EXTRACT(YEAR FROM datum) AS year, 
			EXTRACT(MONTH FROM datum) AS month, 
			SUM(verbruik_gas) AS verbruik_gas,
			SUM(verbruik_water) AS verbruik_water,
			SUM(verbruik_elektra_e1) AS verbruik_elektra_e1,
			SUM(verbruik_elektra_e2) AS verbruik_elektra_e2
			FROM verbruik
			GROUP BY year, month
			ORDER BY year ASC, month ASC";

		$statement = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
		$statement->execute();

		return $statement->fetchAll();
	}

	public function getPerJaar () {
		$sql = "SELECT
			EXTRACT(YEAR FROM datum) AS year, 
			SUM(verbruik_gas) AS verbruik_gas,
			SUM(verbruik_water) AS verbruik_water,
			SUM(verbruik_elektra_e1) AS verbruik_elektra_e1,
			SUM(verbruik_elektra_e2) AS verbruik_elektra_e2
			FROM verbruik
			GROUP BY year
			ORDER BY year ASC";

		$statement = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
		$statement->execute();

		return $statement->fetchAll();
	}

	public function clearVerbruik () {
		$this->db->exec("TRUNCATE TABLE " . $this->datalayer->quoteIdentifier("verbruik"));
	}

	public function insertVerbruik(\business\model\Verbruik $verbruik) {
		$sql = "INSERT INTO " . $this->datalayer->quoteIdentifier("verbruik") . " (
		" . $this->datalayer->quoteIdentifier("datum") . ", 
		" . $this->datalayer->quoteIdentifier("verbruik_water") . ", 
		" . $this->datalayer->quoteIdentifier("verbruik_gas") . ", 
		" . $this->datalayer->quoteIdentifier("verbruik_elektra_e1") . ", 
		" . $this->datalayer->quoteIdentifier("verbruik_elektra_e2") . "
		) VALUES (:datum, :water, :gas, :elektraE1, :elektraE2)";

		$statement = $this->db->prepare($sql);

		$ret = false;
		try {
			$ret = $statement->execute((array)$verbruik);
		} catch (\Exception $e) {
		}

		if (!$ret) {
			$errorInfo = $statement->errorInfo();
			$errorCode = $errorInfo['0'];
			$errorMessage = $errorInfo['2'];

			if ($errorCode == '23505') {
				throw new \datalayer\DuplicateVerbruikException();
			} else {
				throw new Exception($errorMessage . ' (' . $errorCode . ')');
			}
		}

		return true;
	}
}