<?php
include ('Config.php');
use Src\Controller\WalletController;
use Src\Controller\TransactionController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if ($uri[1] !== 'wallets' && $uri[1] !== 'transactions') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($uri[1] == 'wallets'){
    // call to related controller to process action details
    $controller = new WalletController($dbConnection, $requestMethod);
    $controller->processRequest();
}
else if($uri[1] == 'transactions'){
    // call to related controller to process action details
    $controller = new TransactionController($dbConnection, $requestMethod);
    $controller->processRequest();
}