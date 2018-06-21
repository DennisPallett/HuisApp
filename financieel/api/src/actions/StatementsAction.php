<?php

class StatementsAction
{
	protected $container;

	public function __construct(Slim\Container $container) {
		$this->container = $container;
	}

    public function __invoke($request, $response, $args) {
        $entries = $this->loadData($request);

        $data = array();
		foreach($entries as $entry) {
			$record = $entry;

			$data[] = $record;
		}

		return $response->withJson($data);
	}


	private function loadData ($request) {
		$params = $request->getQueryParams();

		$bindParams = array();

		$sql = "SELECT 
			statement.*,
			(SELECT COUNT(*) FROM entry WHERE statement_id = statement.id) AS entry_count
		FROM statement
		WHERE 1=1
		";

		if (!empty($params['month']) && is_numeric($params['month'])) {
			$sql .= " AND date_part('month', start_balance_date) = :month";
			$bindParams[':month'] = $params['month'];
		}

		if (!empty($params['year']) && is_numeric($params['year'])) {
			$sql .= " AND date_part('year', start_balance_date) = :year";
			$bindParams[':year'] = $params['year'];
		}

		$sortBy = "";
		$sortOrder = "";

		if (!empty($params['sortby'])) $sortBy = $params['sortby'];
		if (!empty($params['sortorder'])) $sortOrder = $params['sortorder'];

		$sortOrder = strtoupper($sortOrder);
		if ($sortOrder != 'ASC' && $sortOrder != 'DESC') $sortOrder = 'ASC';

		if (!in_array($sortBy, array('start_balance_date'))) $sortBy = 'start_balance_date';

		$sql .= " ORDER BY " . $sortBy . ' ' . $sortOrder;
		
		$statement = $this->container->db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$statement->execute($bindParams);

		return $statement->fetchAll();
	}
}