<?php

class MeterstandenAction
{
	protected $container;

	public function __construct(Slim\Container $container) {
		$this->container = $container;
	}

    public function getList($request, $response, $args) {
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

	public function getSingle ($request, $response, $args) {
		if (empty($args['opnameDatum'])) {
			return $response
				->withJson(array('code' => 'MISSING_OPNAMEDATUM', 'message' => 'Invalid/missing opnameDatum'))
				->withStatus(400);
		}

		$opnameDatum = $args['opnameDatum'];

		$meterstand = $this->container->dataLayer->getMeterstandenData()->getMeterstand($opnameDatum);
		if ($meterstand == null) {
			return $response
				->withJson(array('code' => 'UNKNOWN_METERSTAND', 'message' => 'No existing meterstand with specified opnameDatum'))
				->withStatus(404);
		}

		$data = array(
			'opnameDatum' => $meterstand->opnameDatum,
			'elektraE1' => $meterstand->elektraE1,
			'elektraE2' => $meterstand->elektraE2,
			'gas' => $meterstand->gas,
			'water' => $meterstand->water
		);

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

		$this->recalculateVerbruik();

		return $response->withJson(true);
	}

	public function update ($request, $response, $args) {
		if (empty($args['opnameDatum'])) {
			return $response
				->withJson(array('code' => 'MISSING_OPNAMEDATUM', 'message' => 'Invalid/missing opnameDatum'))
				->withStatus(400);
		}

		$opnameDatum = $args['opnameDatum'];

		$meterstand = $this->container->dataLayer->getMeterstandenData()->getMeterstand($opnameDatum);
		if ($meterstand == null) {
			return $response
				->withJson(array('code' => 'UNKNOWN_METERSTAND', 'message' => 'No existing meterstand with specified opnameDatum'))
				->withStatus(404);
		}

		$params = $request->getParsedBody();
		$meterstand->setProperties($params);

		$validator = new \business\validators\MeterstandValidator();

		if (!$validator->isValid($meterstand))
		{
			return $response
				->withJson(array('code' => 'INVALID', 'message' => 'Invalid/missing data provided'))
				->withStatus(400);
		}

		try {
			$this->container->dataLayer->getMeterstandenData()->updateMeterstand($opnameDatum, $meterstand);
		} catch (\datalayer\DuplicateMeterstandException $ex) {
			return $response
					->withJson(array('code' => 'DUPLICATE', 'message' => 'Meterstand already exists for opnamedatum'))
					->withStatus(422);
		}

		$this->recalculateVerbruik();

		return $response->withJson(true);
	}

	public function delete ($request, $response, $args) {
		if (empty($args['opnameDatum'])) {
			return $response
				->withJson(array('code' => 'MISSING_OPNAMEDATUM', 'message' => 'Invalid/missing opnameDatum'))
				->withStatus(400);
		}

		$this->container->dataLayer->getMeterstandenData()->deleteMeterstand($args['opnameDatum']);

		$this->recalculateVerbruik();

		return $response->withJson(true);
	}

	private function recalculateVerbruik () {
		$this->container->dataLayer->getMeterstandenData()->clearVerbruik();

		$records = $this->container->dataLayer->getMeterstandenData()->getMeterstanden("opname_datum", "ASC");
		$meterstanden = array();
		foreach($records as $record) {
			$meterstanden[] = $record;
		}

		// TODO: move business logic to a business layer
		for($i=0; $i < count($meterstanden); $i++) {
			if ($i == count($meterstanden)-1) continue;

			$currentMeterstand = $meterstanden[$i];
			$nextMeterstand = $meterstanden[$i+1];

			$period = new \DatePeriod(new \DateTime($currentMeterstand['opname_datum']), new \DateInterval('P1D'), new \DateTime($nextMeterstand['opname_datum']));

			$days = array();
			foreach ($period as $key => $value) {
				$days[] = $value->format('Y-m-d');
			}

			$verbruikElektraE1 = ($nextMeterstand['stand_elektra_e1'] - $currentMeterstand['stand_elektra_e1']) / count($days);
			$verbruikElektraE2 = ($nextMeterstand['stand_elektra_e2'] - $currentMeterstand['stand_elektra_e2']) / count($days);
			$verbruikWater = ($nextMeterstand['stand_water'] - $currentMeterstand['stand_water']) / count($days);
			$verbruikGas = ($nextMeterstand['stand_gas'] - $currentMeterstand['stand_gas']) / count($days);

			foreach($days as $day) {
				$verbruik = new \business\model\Verbruik();
				$verbruik->datum = $day;
				$verbruik->elektraE1 = $verbruikElektraE1;
				$verbruik->elektraE2 = $verbruikElektraE2;
				$verbruik->water = $verbruikWater;
				$verbruik->gas = $verbruikGas;

				$this->container->dataLayer->getMeterstandenData()->insertVerbruik($verbruik);
			}
		}
	}
}