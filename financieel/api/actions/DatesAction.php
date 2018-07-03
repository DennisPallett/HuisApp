<?php

class DatesAction
{
   protected $container;

   public function __construct(Slim\Container $container) {
       $this->container = $container;
   }

   public function months($request, $response, $args) {
		$months = $this->container->db->query("
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

		$data = array();
		foreach($months as $month) {
			$record = array();
			$record['month'] = $month['month'];
			$record['year'] = $month['year'];

			$data[] = $record;
		}

		return $response->withJson($data);
   }
}