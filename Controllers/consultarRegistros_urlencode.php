<?php
// Mostrar errores (solo para desarrollo, quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../Controllers/bd.php"); // Asegúrate que $conn esté definido aquí
header('Content-Type: application/json');

// Autenticación básica
$valid_user = "Admin_fanafesa";
$valid_password = "F4n4f3s4_2025";

if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_password) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(["error" => "Credenciales inválidas."]);
    exit;
}

// Validar parámetros
if (!isset($_GET['proyecto']) || !is_numeric($_GET['proyecto'])) {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(["error" => "El parámetro 'proyecto' es obligatorio y debe ser un número."]);
    exit;
}

if (!isset($_GET['ubicacion']) || !is_numeric($_GET['ubicacion'])) {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(["error" => "El parámetro 'ubicacion' es obligatorio y debe ser un número."]);
    exit;
}

$proyecto = intval($_GET['proyecto']);
$ubicacion = intval($_GET['ubicacion']);

// Preparar la consulta usando parámetros para evitar SQL injection
$sql = "
SELECT
    cyc.id_cyc,
    cyc.nombre,
    cyc.no_ticket,
    CASE cyc.tipo_cyc 
        WHEN 1 THEN 'Crisis'
        WHEN 2 THEN 'Contingencia'
        ELSE 'Desconocido'
    END AS tipo_cyc,
    cyc.ubicacion_cyc,
    cyc.redaccion_cyc,
    cyc.canal_cyc,
    cyc.bot_cyc,
    cyc.fecha_registro_cyc,
    CASE cyc.status_cyc 
        WHEN '1' THEN 'Activo'
        WHEN '0' THEN 'Desactivado'
        ELSE 'Desconocido'
    END AS status_cyc,
    cyc.fecha_programacion,
    u.nombre_usuario,
    cyc.redaccion_canales,
    cyc.proyecto
FROM `cyc` AS cyc
INNER JOIN `usuarios` AS u ON cyc.id_usuario = u.idUsuarios
WHERE cyc.proyecto = ? AND cyc.ubicacion_cyc = ? AND cyc.status_cyc = 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Error en la preparación: " . $conn->error);
    http_response_code(500);
    echo json_encode(["error" => "Error en la preparación de la consulta.", "sql_error" => $conn->error]);
    exit;
}

$stmt->bind_param("ii", $proyecto, $ubicacion);

$stmt->execute();

$result = $stmt->get_result();

$records = [];

while ($row = $result->fetch_assoc()) {
    $message = "{$row['tipo_cyc']} Registrada {$row['redaccion_cyc']} {$row['nombre']} con el número de ticket {$row['no_ticket']}";
    $records[] = ["message" => $message];
}

echo json_encode($records);

$stmt->close();
$conn->close();
?>
