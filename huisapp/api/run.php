<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$basePath = dirname(__FILE__);

require $basePath . '/vendor/autoload.php';

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

$container['dataLayer'] = function ($c) {
	$db = $c['settings']['db'];

	$dbType = substr($db['connectionString'], 0, stripos($db['connectionString'], ':'));

	switch($dbType) {
		case 'pgsql':
			return new datalayer\postgres\DataLayer($db['connectionString'], $db['user'], $db['password']);
		case 'mysql':
			return new datalayer\mysql\DataLayer($db['connectionString'], $db['user'], $db['password']);
		default:
			throw new Exception('Unsupported database type: ' . $dbType);
	}
};

$app->get('/meterstanden', MeterstandenAction::class);
$app->post('/meterstanden/insert', MeterstandenAction::class . ':insert');

$app->run();
