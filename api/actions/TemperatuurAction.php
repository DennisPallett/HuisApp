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
			$data[] = array(
				'jaar' => (int)$record['year'],
				'maand' => (int)$record['month'],
				'avg_temp_indoor' => (double)$record['avg_temp_indoor'],
				'min_temp_indoor' =>  (double)$record['min_temp_indoor'],
				'max_temp_indoor' =>  (double)$record['max_temp_indoor'],
				'avg_temp_1' => (double)$record['avg_temp_1'],
				'min_temp_1' =>  (double)$record['min_temp_1'],
				'max_temp_1' =>  (double)$record['max_temp_1'],
				'avg_temp_2' => (double)$record['avg_temp_2'],
				'min_temp_2' =>  (double)$record['min_temp_2'],
				'max_temp_2' =>  (double)$record['max_temp_2'],
				'avg_temp_3' => (double)$record['avg_temp_3'],
				'min_temp_3' =>  (double)$record['min_temp_3'],
				'max_temp_3' =>  (double)$record['max_temp_3'],
				'avg_temp_4' => (double)$record['avg_temp_4'],
				'min_temp_4' =>  (double)$record['min_temp_4'],
				'max_temp_4' =>  (double)$record['max_temp_4']
			);
		}

		return $response->withJson($data);
	}

	public function getPerDag ($request, $response, $args) {
		$records = $this->container->dataLayer->getTemperatuurData()->getPerDag();

		$data = array();
		foreach($records as $record) {
			$data[] = array(
				'jaar' => (int)$record['year'],
				'maand' => (int)$record['month'],
				'dag'	=> (int)$record['day'],
				'avg_temp_indoor' => (double)$record['avg_temp_indoor'],
				'min_temp_indoor' =>  (double)$record['min_temp_indoor'],
				'max_temp_indoor' =>  (double)$record['max_temp_indoor'],
				'avg_temp_1' => (double)$record['avg_temp_1'],
				'min_temp_1' =>  (double)$record['min_temp_1'],
				'max_temp_1' =>  (double)$record['max_temp_1'],
				'avg_temp_2' => (double)$record['avg_temp_2'],
				'min_temp_2' =>  (double)$record['min_temp_2'],
				'max_temp_2' =>  (double)$record['max_temp_2'],
				'avg_temp_3' => (double)$record['avg_temp_3'],
				'min_temp_3' =>  (double)$record['min_temp_3'],
				'max_temp_3' =>  (double)$record['max_temp_3'],
				'avg_temp_4' => (double)$record['avg_temp_4'],
				'min_temp_4' =>  (double)$record['min_temp_4'],
				'max_temp_4' =>  (double)$record['max_temp_4']
			);
		}

		return $response->withJson($data);
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