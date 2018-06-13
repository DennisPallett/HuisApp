<?php

class ReportingAction
{
   protected $container;

   public function __construct(Slim\Container $container) {
       $this->container = $container;
   }

   public function categoryByMonth($request, $response, $args) {
		$records = $this->container->db->query("
			SELECT 
				date_part('year', value_date) as year,
				date_part('month', value_date) AS month,
				CASE WHEN(amount > 0) THEN 'inkomen' ELSE 'lasten' END AS stack,
				sum(amount) AS total_amount
			FROM entry
			GROUP BY 
				date_part('year', value_date),
				date_part('month', value_date),
				CASE WHEN(amount > 0) THEN 'inkomen' ELSE 'lasten' END
			ORDER BY
				date_part('year', value_date) DESC,
				date_part('month', value_date) DESC
		");

		$series = array();
		$categories = array();
		foreach($records as $record) {
			$yearMonth = $record['year'] .'-' . $record['month'];

			$categories[] = $yearMonth;

			$category = $record['stack'];

			if (!isset($series[$category])) $series[$category] = array();

			$series[$category][$yearMonth] = $record['total_amount'];
		}

		$categories = array_unique($categories);

		$data['series'] = array();
		foreach($series as $serieName => $values) {
			$serie = array('name' => $serieName, 'data' => array());

			foreach($categories as $category) {
				$value = (double)((isset($values[$category])) ? $values[$category] : 0);
				$value = abs($value);
				$serie['data'][] = $value;
			}

			$data['series'][] = $serie;
		}

		$data['categories'] = array();
		foreach($categories as $category) {
			$data['categories'][] = strftime('%b %Y', strtotime($category . '-01'));
		}

		return $response->withJson($data);
   }
}