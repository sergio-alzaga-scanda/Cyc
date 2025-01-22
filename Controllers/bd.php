<?php
// Datos de conexión
$serverName = "localhost\SQLEXPRESS";  // Nombre del servidor o IP de la base de datos
$connectionOptions = array(
    "Database" => "contingencias", // Nombre de la base de datos
    "Uid" => "dev",             // Nombre de usuario
    "PWD" => "Ser132gio."           // Contraseña del usuario
);

try {
    
    $conn = new PDO("sqlsrv:server=$serverName;Database=" . $connectionOptions['Database'], 
                    $connectionOptions['Uid'], 
                    $connectionOptions['PWD']);
    
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mensaje opcional de éxito
    // echo "Conexión exitosa!";
} catch (PDOException $e) {
    // Manejo de errores si la conexión falla
    die("Error de conexión: " . $e->getMessage());
}
?>
