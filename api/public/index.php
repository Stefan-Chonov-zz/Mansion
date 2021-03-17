<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', strtolower($uri));

unset($uri[0]);

$uri = array_values($uri);

$supportedApiEndpoints = require_once __DIR__ . '/../src/Utils/Endpoints.php';
if (!array_key_exists($uri[0], $supportedApiEndpoints) ||
    !array_key_exists($uri[1], array_flip($supportedApiEndpoints[$uri[0]]))) {
    header("HTTP/1.1 404 Not Found");

    $response = array(
        'status' => 404,
        'message' => "Invalid endpoint",
        'data' => array()
    );

    echo json_encode($response);
    die();
}

$app = new Mansion\Api();
$app->handleRequest();