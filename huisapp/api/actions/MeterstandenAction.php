<?php

class MeterstandenAction
{
	protected $container;

	public function __construct(Slim\Container $container) {
		$this->container = $container;
	}

    public function __invoke($request, $response, $args) {
		$params = $request->getQueryParams();

		$sortBy = "";
		$sortOrder = "";

		if (!empty($params['sortby'])) $sortBy = $params['sortby'];
		if (!empty($params['sortorder'])) $sortOrder = $params['sortorder'];

		$sortOrder = strtoupper($sortOrder);
		if ($sortOrder != 'ASC' && $sortOrder != 'DESC') $sortOrder = 'ASC';

		if (!in_array($sortBy, array('opname_datum'))) $sortBy = 'opname_datum';

        $records = $this->container->dataLayer->getMeterstandenData()->getMeterstanden($sortBy, $sortOrder);

        $data = array();
		foreach($records as $record) {
			$data[] = array(
				'opnameDatum' => $record['opname_datum'],
				'elektraE1' => $record['stand_elektra_e1'],
				'elektraE2' =>  $record['stand_elektra_e2'],
				'gas' =>  $record['stand_gas'],
				'water' =>  $record['stand_water']
			);
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
				->withJson(array('code' => 'INVALID', 'message' => 'Invalid/missing data provided'))
				->withStatus(400);
		}

		try {
			$this->container->dataLayer->getMeterstandenData()->insertMeterstand($meterstand);
		} catch (\datalayer\DuplicateMeterstandException $ex) {
			return $response
					->withJson(array('code' => 'DUPLICATE', 'message' => 'Meterstand already exists for opnamedatum'))
					->withStatus(422);
		}

		return $response->withJson(true);
	}
}