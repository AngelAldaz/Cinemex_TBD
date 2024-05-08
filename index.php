<?php
//ACCEDER A LAS VARIABLES DE ENTORNO ENCONTRADAS EN EL ARCHIVO '.env'
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Variables de entorno</title>
  <link rel="stylesheet" href="src/styles/style.css">
</head>

<body>
  <div class="p-5 space-y-5">
    <h1 class="text-7xl text-blue-700">Variables de entorno</h1>
    <h2 class="text-xl"><span class="font-bold">Host:</span> <?= $dbHost; ?></h2>
    <h2 class="text-xl"><span class="font-bold">User:</span> <?= $dbUser; ?></h2>
    <h2 class="text-xl"><span class="font-bold">Password:</span> <?= $dbPass; ?></h2>
  </div>
</body>

</html>