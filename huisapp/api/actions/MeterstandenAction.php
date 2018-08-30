<?php

class MeterstandenAction
{
	protected $container;

	public function __construct(Slim\Container $container) {
		$this->container = $container;
	}

    public function __invoke($request, $response, $args) {
		$params = $request->getQueryParams();

		$year = null;
		$month = null;

		if (!empty($params['month']) && is_numeric($params['month'])) 
			$month = $params['month'];

		if (!empty($params['year']) && is_numeric($params['year'])) 
			$year = $params['year'];

		$sortBy = "";
		$sortOrder = "";

		if (!empty($params['sortby'])) $sortBy = $params['sortby'];
		if (!empty($params['sortorder'])) $sortOrder = $params['sortorder'];

		$sortOrder = strtoupper($sortOrder);
		if ($sortOrder != 'ASC' && $sortOrder != 'DESC') $sortOrder = 'ASC';

		if (!in_array($sortBy, array('opname_datum'))) $sortBy = 'opname_datum';

        $records = $this->container->dataLayer->getMeterstandenData()->getMeterstanden($year, $month, $sortBy, $sortOrder);

        $data = array();
		foreach($records as $record) {
			$data[] = $record;
		}

		return $response->withJson($data);
	}

	public function insert ($request, $response, $args) {
		$params = $request->getParsedBody();

		$meterstand = new \business\model\Meterstand();
		$meterstand->setProperties($params);

		$validator = new \business\validators\MeterstandValidator();

		if (!$validator->isValid($meterstand))
		{
			return $response
				->withJson(array('error' => array('code' => 'INVALID', 'message' => 'Invalid/missing data provided')))
				->withStatus(400);
		}

		try {
			$this->container->dataLayer->getMeterstandenData()->insertMeterstand($meterstand);
		} catch (\datalayer\DuplicateMeterstandException $ex) {
			return $response
					->withJson(array('error' => array('code' => 'DUPLICATE', 'message' => 'Meterstand already exists for opnamedatum')))
					->withStatus(422);
		}

		return $response->withJson(true);
	}
}