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
				'jaar' => $record['year'],
				'maand' => $record['month'],
				'elektraE1' => $record['verbruik_elektra_e1'],
				'elektraE2' =>  $record['verbruik_elektra_e2'],
				'gas' =>  $record['verbruik_gas'],
				'water' =>  $record['verbruik_water']
			);
		}

		return $response->withJson($data);
	}

}