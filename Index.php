<?php

declare(strict_types=1);
include('Config.php');

use Src\Controller\WalletController;
use Src\Controller\TransactionController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($uri[1] !== 'wallets' && $uri[1] !== 'transactions') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($uri[1] == 'wallets') {
    // call to related controller to process action details
    $controller = new WalletController($dbConnection);
    switch ($requestMethod) {
        case 'GET':
            $response = $controller->getAllWallets();
            break;
        case 'POST':
            $response = $controller->createWalletFromRequest($_POST);
            break;
        case 'DELETE':
            $input = (array)json_decode(file_get_contents('php://input'), TRUE);
            $response = $controller->deleteWallet($input);
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            exit();
            break;
    }
} elseif ($uri[1] == 'transactions') {
    // call to related controller to process action details
    $controller = new TransactionController($dbConnection);
    switch ($requestMethod) {
        case 'GET':
            $response = $controller->getAllTransactions();
            break;
        case 'POST':
            $response = $controller->createTransactionFromRequest($_POST);
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            exit();
            break;
    }
}

header($response['status_code_header']);
if ($response['body']) {
    echo $response['body'];
}
