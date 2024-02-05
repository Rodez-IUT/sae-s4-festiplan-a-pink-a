<?php
const PREFIX_TO_RELATIVE_PATH = "/festiplan";
require $_SERVER[ 'DOCUMENT_ROOT' ] . PREFIX_TO_RELATIVE_PATH . '/lib/vendor/autoload.php';

use application\DefaultComponentFactory;
use yasmf\DataSource;
use yasmf\Router;

$dataSource = new DataSource(
    $host = 'mysql-festiplanbfgi.alwaysdata.net',
    $port = '3306', 
    $db = 'festiplanbfgi_sae', 
    $user = '343265', 
    $pass = 'pligraHEpru6', 
    $charset = 'utf8mb4'
);

$router = new Router(new DefaultComponentFactory(), $dataSource) ;
$router->route(PREFIX_TO_RELATIVE_PATH, $dataSource);
