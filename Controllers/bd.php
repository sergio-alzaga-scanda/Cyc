<?php
// Datos de conexión
$serverName = "localhost";  // Docker SQL Server
$connectionOptions = array(
    "Database" => "contingencias",
    "Uid" => "sa",
    "PWD" => "CrisisSQL2024!"
);

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=" . $connectionOptions['Database'],
                    $connectionOptions['Uid'],
                    $connectionOptions['PWD']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
