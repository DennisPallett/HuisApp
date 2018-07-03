<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$basePath = dirname(__FILE__);

require $basePath . '/vendor/autoload.php';

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

$container['db'] = function ($c) {
	$db = $c['settings']['db'];
    $pdo = new PDO($db['connectionString'], $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	if (substr($db['connectionString'], 0, strlen('mysql')) == 'mysql') {
		$pdo->query("SET SESSION sql_warnings=1");
		$pdo->query("SET NAMES utf8");
		$pdo->query("SET SESSION sql_mode = \"ANSI,TRADITIONAL\"");
	}

    return $pdo;
};

$app->get('/transacties', TransactiesAction::class);

$app->get('/statements', StatementsAction::class);
$app->post('/statements/delete', StatementsAction::class . ':delete');
$app->post('/statements/import', StatementsAction::class . ':import');

$app->post('/transacties/classify', TransactiesAction::class . ':classify');
$app->post('/transacties/update-category', TransactiesAction::class . ':updateCategory');

$app->get('/categories', CategoriesAction::class);
$app->get('/categories/groups', CategoriesAction::class . ':groups');

$app->get('/dates/months', DatesAction::class  . ':months');

$app->get('/reporting/category-by-month', ReportingAction::class  . ':categoryByMonth');
$app->get('/reporting/balance', ReportingAction::class  . ':balance');

$app->run();
