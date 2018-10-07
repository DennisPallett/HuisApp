<?php

class TemperatuurAction
{
	protected $container;

	public function __construct(Slim\Container $container) {
		$this->container = $container;
	}

	public function getPerMaand ($request, $response, $args) {
		$records = $this->container->dataLayer->getTemperatuurData()->getPerMaand();

		$data = array();
		foreach($records as $record) {
			$data[] = $this->convertRecordToOutput($record, array(
				'jaar' => (int)$record['year'],
				'maand' => (int)$record['month'],
			));
		}

		return $response->withJson($data);
	}

	public function getPerDag ($request, $response, $args) {
		$params = $request->getQueryParams();

		$year = null;
		$month = null;

		if (!empty($params['month']) && is_numeric($params['month'])) 
			$month = $params['month'];

		if (!empty($params['year']) && is_numeric($params['year'])) 
			$year = $params['year'];

		$records = $this->container->dataLayer->getTemperatuurData()->getPerDag($year, $month);

		$data = array();
		foreach($records as $record) {
			$data[] = $this->convertRecordToOutput($record, array(
				'jaar' => (int)$record['year'],
				'maand' => (int)$record['month'],
				'dag'	=> (int)$record['day']
			));
		}

		return $response->withJson($data);
	}

	public function getPerUur ($request, $response, $args) {
		$params = $request->getQueryParams();

		$year = null;
		$month = null;

		if (empty($params['month']) || !is_numeric($params['month'])) 
		{
			return $response
				->withJson(array('code' => 'MISSING_MONTH_PARAMETER', 'message' => 'Month parameter is empty or missing.'))
				->withStatus(400);
		}

		if (empty($params['year']) || !is_numeric($params['year'])) 
		{
			return $response
				->withJson(array('code' => 'MISSING_YEAR_PARAMETER', 'message' => 'Year parameter is empty or missing.'))
				->withStatus(400);
		}

		$month = $params['month'];
		$year = $params['year'];

		$records = $this->container->dataLayer->getTemperatuurData()->getPerUur($year, $month);

		$data = array();
		foreach($records as $record) {
			$data[] = $this->convertRecordToOutput($record, array(
				'jaar' => (int)$record['year'],
				'maand' => (int)$record['month'],
				'dag'	=> (int)$record['day'],
				'uur'	=> (int)$record['hour']
			));
		}

		return $response->withJson($data);
	}

	private function toDoubleOrNull ($value) {
		return ($value == null) ? null : (double)$value;
	}

	private function convertRecordToOutput($record, $output) {
		return array_merge($output, array(
			'avg_temp_indoor' => $this->toDoubleOrNull($record['avg_temp_indoor']),
			'min_temp_indoor' =>  $this->toDoubleOrNull($record['min_temp_indoor']),
			'max_temp_indoor' =>  $this->toDoubleOrNull($record['max_temp_indoor']),
			'avg_temp_1' => $this->toDoubleOrNull($record['avg_temp_1']),
			'min_temp_1' =>  $this->toDoubleOrNull($record['min_temp_1']),
			'max_temp_1' =>  $this->toDoubleOrNull($record['max_temp_1']),
			'avg_temp_2' => $this->toDoubleOrNull($record['avg_temp_2']),
			'min_temp_2' =>  $this->toDoubleOrNull($record['min_temp_2']),
			'max_temp_2' =>  $this->toDoubleOrNull($record['max_temp_2']),
			'avg_temp_3' => $this->toDoubleOrNull($record['avg_temp_3']),
			'min_temp_3' =>  $this->toDoubleOrNull($record['min_temp_3']),
			'max_temp_3' =>  $this->toDoubleOrNull($record['max_temp_3']),
			'avg_temp_4' => $this->toDoubleOrNull($record['avg_temp_4']),
			'min_temp_4' =>  $this->toDoubleOrNull($record['min_temp_4']),
			'max_temp_4' =>  $this->toDoubleOrNull($record['max_temp_4'])
		));
	}

	public function import ($request, $response, $args) {
		$files = $request->getUploadedFiles();

		if (empty($files)) {
			return $response
				->withJson(array('code' => 'MISSING_FILES', 'message' => 'No files have been uploaded.'))
				->withStatus(400);
		}

		$importDataLayer = $this->container->dataLayer->getTemperatuurData();
		$importer = new Importer($importDataLayer);

		// get first uploaded file (only single file upload is supported)
		$file = array_shift($files);

		$fileName = $file->getClientFilename();

		if ($file->getError() !== UPLOAD_ERR_OK) {
			return $response
				->withJson(array('code' => 'UPLOAD_FAILED', 'message' => 'Upload failed.'))
				->withStatus(400);
		}

		$error = null;
		try {
			$importer->importFile($file->file);
		} catch (Exception $e) {
			return $response
				->withJson(array('code' => $e->getCode(), 'message' => $e->getMessage()))
				->withStatus(500);
		}

		$result = array('importCount' => $importer->importCount, 'duplicateCount' => $importer->duplicateCount);
		return $response->withJson($result);
	}

}