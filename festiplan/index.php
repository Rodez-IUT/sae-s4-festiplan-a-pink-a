<?php
const PREFIX_TO_RELATIVE_PATH = "/festiplan";
require $_SERVER['DOCUMENT_ROOT'] . PREFIX_TO_RELATIVE_PATH . '/lib/vendor/autoload.php';

use application\DefaultComponentFactory;
use yasmf\DataSource;
use yasmf\Router;

session_start();

$dataSource = new DataSource(
    $host = 'SAE_S3_DevWeb_db',
    $port = '3306', 
    $db = 'festiplanbfgi_sae', 
    $user = 'root', 
    $pass = 'root', 
    $charset = 'utf8mb4'
);

$router = new Router(new DefaultComponentFactory());
$router->route(PREFIX_TO_RELATIVE_PATH, $dataSource);
