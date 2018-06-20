<?php

class CategoriesAction
{
   protected $container;

   public function __construct(Slim\Container $container) {
       $this->container = $container;
   }

   public function groups ($request, $response, $args) {
		$records = $this->container->db->query("
			SELECT 
				cg.key AS categorygroup_key,
				COALESCE(cg.name, 'Misc') AS categorygroup_name,
				c.key AS category_key,
				c.name AS category_name
			FROM categorygroup cg
			FULL OUTER JOIN category c ON c.group = cg.key
			ORDER BY
				cg.name ASC
		");

		$data = array();
		foreach($records as $record) {
			$groupKey = $record['categorygroup_key'];

			if (!isset($data[$groupKey])) {
				$data[$groupKey] = array('key' => $groupKey, 'name' => $record['categorygroup_name'], 'categories' => array());
			}

			$data[$groupKey]['categories'][] = array('key' => $record['category_key'], 'name' => $record['category_name']);
		}

		$data = array_values($data);

		return $response->withJson($data);
   }

   public function __invoke($request, $response, $args) {
		$categories = $this->container->db->query("
			SELECT 
				key,
				name
			FROM category
			ORDER BY
				name ASC
		");

		$data = array();
		foreach($categories as $category) {
			$record = $category;

			$data[] = $record;
		}

		return $response->withJson($data);
   }
}