<?php
require ('../../lib/importer/Importer.php');

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

	public function import ($request, $response, $args) {
		$files = $request->getUploadedFiles();

		if (empty($files)) return $response->withJson("Missing files")->withStatus(400);

		$importer = new Importer($this->container->db);

		$result = array();
		foreach($files as $file) {
			$fileName = $file->getClientFilename();

			if ($file->getError() !== UPLOAD_ERR_OK) {
				$result[] = array('name' => $fileName, 'success' => false, 'error' => 'Upload not OK');
				continue;
			}

			$error = null;
			try {
				$importer->importFile($file->file);
			} catch (DuplicateStatementException $e) {
				$error = "Statement already present in database";
			} catch (Exception $e) {
				$error = $e->getMessage();
			}

			$result[] = array('name' => $fileName, 'success' => ($error == null), 'error' => $error);
		}

		return $response->withJson($result);
	}

	public function delete($request, $response, $args) {
		$params = $request->getParsedBody();

		if (empty($params['month'])) {
			return $response->withJson("Missing month parameter")->withStatus(400);
		}

		if (empty($params['year'])) {
			return $response->withJson("Missing year parameter")->withStatus(400);
		}

		if (!is_numeric($params['month'])) {
			return $response->withJson("Invalid month parameter; must be numeric")->withStatus(400);
		}

		if (!is_numeric($params['year'])) {
			return $response->withStatus(400)->withJson("Invalid year parameter; must be numeric");
		}

		$this->deleteTransactions($params['month'], $params['year']);
		$this->deleteStatements($params['month'], $params['year']);

		return $response->withJson(true);
	}

	private function deleteTransactions($month, $year) {
		$bindParams = array();

		$sql = "DELETE FROM entry WHERE statement_id IN (
			SELECT id FROM statement WHERE
				date_part('month', start_balance_date) = :month
				AND date_part('year', start_balance_date) = :year
		)";

		$bindParams[':month'] = $month;
		$bindParams[':year'] = $year;

		$statement = $this->container->db->prepare($sql);
		$statement->execute($bindParams);
	}

	private function deleteStatements($month, $year) {
		$bindParams = array();

		$sql = "DELETE FROM statement WHERE
				date_part('month', start_balance_date) = :month
				AND date_part('year', start_balance_date) = :year";

		$bindParams[':month'] = $month;
		$bindParams[':year'] = $year;

		$statement = $this->container->db->prepare($sql);
		$statement->execute($bindParams);
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