<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'config/db_config.php';

$app = new \Slim\App;

/*$app->get('/', function() {
  return 'Hello World';
});

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});*/

require_once('app/api/common_calls.php');

require_once('app/api/users/index.php');
require_once('app/api/doctors/index.php');
require_once('app/api/patients/index.php');

require_once('app/api/categories/index.php');
require_once('app/api/items/index.php');
require_once('app/api/products/index.php');
require_once('app/api/orders/index.php');
require_once('app/api/orders_processing/index.php');
require_once('app/api/collectors/index.php');

require_once('app/api/dashboard/index.php');
require_once('app/api/reports/index.php');


$app->run();
