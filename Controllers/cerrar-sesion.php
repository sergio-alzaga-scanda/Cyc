<?php
session_start();

require_once '../Controllers/bd.php'; // Ajusta la ruta al archivo de conexión

// Obtener datos de la sesión antes de destruirla
$id_usuario     = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_usuario'];
$proyecto       = $_SESSION['proyecto'] ?? null; // Puede ser null si no existe

// Insertar en la tabla logs antes de cerrar la sesión, incluyendo la columna proyecto
$queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) 
             VALUES (NOW(), ?, ?, ?, ?)";
$stmtLog = $conn->prepare($queryLog);
if (!$stmtLog) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$descripcion = 'Cerró sesión';

// bind_param: tipos "isss" -> i=int, s=string; asumimos user_id es int y los demás string
$stmtLog->bind_param("isss", $id_usuario, $nombre_usuario, $descripcion, $proyecto);
$stmtLog->execute();
$stmtLog->close();

$_SESSION['usuario']        = '';
$_SESSION['nombre_usuario'] = '';
$_SESSION['proyecto']       = '';
session_unset();
session_destroy();

header("Location: ../index.php");
exit();
?>
