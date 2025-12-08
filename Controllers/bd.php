<?php
// Datos de conexión
// $servername = "localhost";
// $port = 3307;
// $username = "root";
// $password = "";
// $database = "Cyc"; 
$servername = "localhost";
$port       = 3306;
$username   = "root";
$password   = "Melco154.,";
$database   = "Cyc";

// Crear conexión con puerto
$conn = new mysqli($servername, $username, $password, $database, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
