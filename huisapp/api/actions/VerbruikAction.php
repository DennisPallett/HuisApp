<?php

class VerbruikAction
{
	protected $container;

	public function __construct(Slim\Container $container) {
		$this->container = $container;
	}

    public function getPerMaand($request, $response, $args) {
		$records = $this->container->dataLayer->getVerbruikData()->getPerMaand();

		$data = array();
		foreach($records as $record) {
			$data[] = array(
				'jaar' => (int)$record['year'],
				'maand' => (int)$record['month'],
				'elektraE1' => (double)$record['verbruik_elektra_e1'],
				'elektraE2' =>  (double)$record['verbruik_elektra_e2'],
				'gas' =>  (double)$record['verbruik_gas'],
				'water' =>  (double)$record['verbruik_water']
			);
		}

		return $response->withJson($data);
	}

}