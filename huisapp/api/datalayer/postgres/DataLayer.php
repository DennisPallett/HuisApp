<?php
namespace datalayer\postgres;

class DataLayer extends \datalayer\database\DataLayer {
	private $db;

	private $meterstandenDataLayer;

	private $verbruikDataLayer;

	public function __construct($connectionString, $user, $password) {
		$this->db = new \PDO($connectionString, $user, $password);
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

		parent::__construct($this->db);

		$this->meterstandenDataLayer = new \datalayer\database\MeterstandenDataLayer($this, $this->db);
		$this->verbruikDataLayer = new \datalayer\database\VerbruikDataLayer($this, $this->db);
	}

	public function getMeterstandenData () : \datalayer\IMeterstandenDataLayer {
		return $this->meterstandenDataLayer;
	}

	public function getVerbruikData () : \datalayer\IVerbruikDataLayer {
		return $this->verbruikDataLayer;
	}

	public function quoteIdentifier ($identifier) {
		$split = explode('.', $identifier);
		$newIdentifier = array();
		foreach($split as $str) {
			$newIdentifier[] = '"' . $str . '"';
		}

		return implode('.', $newIdentifier);
	}
}