<?php
session_start();

require_once '../Controllers/bd.php'; // Ajusta la ruta al archivo de conexión

// Obtener datos de la sesión antes de destruirla
$id_usuario     = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_usuario'];

// // Insertar en la tabla logs antes de cerrar la sesión
// $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
//              VALUES (GETDATE(), :user_id, :name_user, :description)";
// $stmtLog = $conn->prepare($queryLog);
// $stmtLog->bindParam(':user_id', $id_usuario);
// $stmtLog->bindParam(':name_user', $nombre_usuario);
// $descripcion = 'Cerró sesión';
// $stmtLog->bindParam(':description', $descripcion);
// $stmtLog->execute();


$_SESSION['usuario']        = '';
$_SESSION['nombre_usuario'] = '';
session_unset();
session_destroy();

header("Location: ../index.php");
exit();
?>
