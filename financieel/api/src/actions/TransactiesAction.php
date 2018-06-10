<?php

class TransactiesAction
{
   protected $container;

   public function __construct(Slim\Container $container) {
       $this->container = $container;
   }

   public function __invoke($request, $response, $args) {
		$entries = $this->loadData($request);

		$data = array();
		foreach($entries as $entry) {
			$record = array();
			$record['amount'] = $entry['amount'];

			$data[] = $entry;
		}

		return $response->withJson($data);
   }

   private function loadData ($request) {
   	   $params = $request->getQueryParams();

	   $bindParams = array();

   	   $sql = "SELECT * FROM entry WHERE 1=1";

	   if (!empty($params['month']) && is_numeric($params['month'])) {
	   	   $sql .= " AND date_part('month', value_date) = :month";
		   $bindParams[':month'] = $params['month'];
	   }

	   if (!empty($params['year']) && is_numeric($params['year'])) {
	   	   $sql .= " AND date_part('year', value_date) = :year";
		   $bindParams[':year'] = $params['year'];
	   }

	   $statement = $this->container->db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	   $statement->execute($bindParams);

	   return $statement->fetchAll();
   }
}