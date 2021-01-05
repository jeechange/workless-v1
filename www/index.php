<?php



use phpex\Foundation\Request;

if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    $_SERVER["HTTP_AJAX"] = "ON";
}

if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) {
    header('Access-Control-Allow-Credentials:true');
    header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Headers:' . $_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]);
    exit;
}

$environment="dev";
define("LATTE_DEV", true);
require_once __DIR__ .'/../app/core.php.cache';
$loader = require_once __DIR__ . '/../app/start.php';
Request::enableHttpMethodParameterOverride();

$appMain = new appMain($environment, true);
$response = $appMain->run();
$response->send();

