<?php
namespace datalayer\postgres;

class DataLayer extends \datalayer\database\DataLayer {
	private $db;

	private $reportingDataLayer;

	private $statementsDataLayer;

	private $importDataLayer;

	private $transactionsDataLayer;

	public function __construct($connectionString, $user, $password) {
		$this->db = new \PDO($connectionString, $user, $password);
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

		parent::__construct($this->db);

		$this->reportingDataLayer = new ReportingDataLayer($this->db);
		$this->statementsDataLayer = new StatementsDataLayer($this->db);
		$this->importDataLayer = new ImportDataLayer($this->db);
		$this->transactionsDataLayer = new TransactionsDataLayer($this->db);
	}

	public function quoteIdentifier ($identifier) {
		return '"' . $identifier . '"';
	}

	function getReportingData () : \datalayer\IReportingDataLayer {
		return $this->reportingDataLayer;
	}

	function getStatementsData() : \datalayer\IStatementsDataLayer {
		return $this->statementsDataLayer;
	}

	function getImportData() : \IImportDataLayer {
		return $this->importDataLayer;
	}

	function getTransactionsData() : \datalayer\ITransactionsDataLayer {
		return $this->transactionsDataLayer;
	}

	function getCategoriesAndGroups () {
		$records = $this->db->query('
			SELECT 
				cg."key" AS categorygroup_key,
				cg.name AS categorygroup_name,
				c."key" AS category_key,
				c.name AS category_name
			FROM categorygroup cg
			LEFT JOIN category c ON c.group = cg.key
			UNION
			SELECT
				\'misc\' AS categorygroup_key,
				\'Misc\' AS categorygroup_name,
				c."key" AS category_key,
				c.name AS category_name
			FROM category c
			WHERE c.group IS NULL
			ORDER BY
				categorygroup_name ASC
		');

		return $records;
	}

	function getCategories () {
		$categories = $this->db->query('
			SELECT 
				"key",
				name
			FROM "category"
			ORDER BY
				name ASC
		');

		return $categories;
	}

	function getMonths () {
		$records = $this->db->query("
			SELECT 
				date_part('year', value_date) as year,
				date_part('month', value_date) AS month
			FROM entry
			GROUP BY 
				date_part('year', value_date),
				date_part('month', value_date)
			ORDER BY
				date_part('year', value_date) DESC,
				date_part('month', value_date) DESC
		");

		return $records;
	}
}