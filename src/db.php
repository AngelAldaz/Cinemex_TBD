<?php

// Conexión a la base de datos (suponiendo que ya tienes esta parte configurada)
$servername = "sfw-division.com";
$username = "u925603734_cine";
$password = "Cinemex123";
$dbname = "u925603734_Cinemex_DB";


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    //echo "Conexion realizada!";
} catch (PDOException $error) {
    echo "Conexion erronea" . $error;
}
