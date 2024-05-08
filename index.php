<?php
require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbHost = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');


?>

<h1><?= $dbHost ?></h1>
<h1><?= $dbUser ?></h1>
<h1><?= $dbPass ?></h1>
