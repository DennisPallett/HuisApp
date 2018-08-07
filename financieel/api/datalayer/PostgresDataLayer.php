<?php
require 'postgres/PostgresReportingDataLayer.php';

class PostgresDataLayer implements IDataLayer {
	private $db;

	private $reportingDataLayer;

	public function __construct($connectionString, $user, $password) {
		$this->db = new PDO($connectionString, $user, $password);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		$this->reportingDataLayer = new PostgresReportingDataLayer($this->db);
	}

	function getBalances () {
		return $this->reportingDataLayer->getBalances();
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