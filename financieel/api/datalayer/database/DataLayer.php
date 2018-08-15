<?php
namespace datalayer\database;

abstract class DataLayer implements \datalayer\IDataLayer {
	private $db;

	protected function __construct($db) {
		$this->db = $db;
	}

	public abstract function quoteIdentifier($identifier);

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
				' . $this->quoteIdentifier("key") . ',
				' . $this->quoteIdentifier("name") . '
			FROM ' . $this->quoteIdentifier("category") . '
			ORDER BY ' . $this->quoteIdentifier("name") . ' ASC
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