<?php

// Support DEBUG cookie
error_reporting(0);
if ($_COOKIE['debug'] == 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(-1);
}

require_once("vendor/autoload.php");

spl_autoload_register(function ($class_name) {
    include 'classes/' . $class_name . '.php';
});

date_default_timezone_set('America/New_York');

$ini = parse_ini_file("config.ini", true)["ww"];

try {
    $pdo = new PDO(
        'mysql:host=' . $ini['db_host'] . ';dbname=' . $ini['db_name'] . ';charset=utf8mb4',
        $ini['db_username'],
        $ini['db_password'],
        array(
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                            PDO::ATTR_PERSISTENT => false
                        )
    );
} catch (Exception $e) {
    http_response_code(500);
    exit($e->getMessage());
}

$config = array(
    'dbo' => $pdo,
    'appName' => 'Wedding Wager',
    'logLocal' => true
);

session_start();
$authHelper = new LoginHelper();
