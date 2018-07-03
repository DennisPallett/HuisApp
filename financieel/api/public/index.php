<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../config.php';

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

$container['db'] = function ($c) {
	$db = $c['settings']['db'];
    $pdo = new PDO($db['connectionString'], $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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
