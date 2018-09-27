<?php

class TemperatuurAction
{
	protected $container;

	public function __construct(Slim\Container $container) {
		$this->container = $container;
	}

	public function import ($request, $response, $args) {
		$files = $request->getUploadedFiles();

		if (empty($files)) {
			return $response
				->withJson(array('code' => 'MISSING_FILES', 'message' => 'No files have been uploaded.'))
				->withStatus(400);
		}

		$importDataLayer = $this->container->dataLayer->getImportData();
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