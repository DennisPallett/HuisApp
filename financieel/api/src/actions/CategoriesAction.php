<?php

class CategoriesAction
{
   protected $container;

   public function __construct(Slim\Container $container) {
       $this->container = $container;
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