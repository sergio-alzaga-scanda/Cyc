<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Es una buena práctica definir la zona horaria
date_default_timezone_set('America/Mexico_City');

include("../Controllers/bd.php");
header('Content-Type: application/json');

// Autenticación básica
$valid_user     = "Admin_fanafesa";
$valid_password = "F4n4f3s4_2025";

if (
    !isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_password
) {
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

$proyecto  = intval($_GET['proyecto']);
$ubicacion = intval($_GET['ubicacion']);


// 1. Actualizar tickets programados cuya fecha de activación ya pasó.
$sql_update = "UPDATE cyc SET status_cyc = 1 WHERE status_cyc = 3 AND fecha_programacion <= NOW()";

$stmt_update = $conn->prepare($sql_update);
if ($stmt_update) {
    $stmt_update->execute();
    $stmt_update->close();
}

// --- PAUSA DE 3 SEGUNDOS AÑADIDA ---
// Se espera 3 segundos para dar tiempo a que la base de datos procese el cambio de estatus.
sleep(3);


// 2. Consulta SQL principal para devolver la respuesta.
$sql = "
SELECT
    cyc.id_cyc,
    cyc.nombre,
    cyc.no_ticket,
    cat_crisis.nombre_crisis,
    cat_crisis.criticidad,
    CASE cyc.tipo_cyc 
        WHEN 1 THEN 'Crisis'
        WHEN 2 THEN 'Contingencia'
        ELSE 'Desconocido'
    END AS tipo_cyc,
    ubicaciones.nombre_ubicacion_ivr AS ubicacion_cyc,
    cyc.redaccion_cyc as grabacion,
    cyc.canal_cyc,
    cyc.bot_cyc,
    cyc.fecha_registro_cyc,
    CASE cyc.status_cyc
        WHEN 1 THEN 'Activo'
        WHEN 2 THEN 'Desactivado'
        ELSE 'Desconocido'
    END AS status_cyc,
    cyc.fecha_programacion,
    cyc.id_usuario,
    usuarios.nombre_usuario,
    cyc.redaccion_canales,
    cyc.proyecto
FROM cyc AS cyc
LEFT JOIN cat_crisis AS cat_crisis
    ON cyc.categoria_cyc = cat_crisis.id
LEFT JOIN ubicacion_ivr AS ubicaciones
    ON cyc.ubicacion_cyc = ubicaciones.id_ubicacion_ivr
LEFT JOIN usuarios AS usuarios
    ON cyc.id_usuario = usuarios.idUsuarios
WHERE cyc.proyecto = ? AND cyc.ubicacion_cyc = ?
AND cyc.status_cyc = 1
";

// Preparar y ejecutar con mysqli
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(["error" => "Error al preparar la consulta.", "details" => $conn->error]);
    exit;
}

$stmt->bind_param("ii", $proyecto, $ubicacion);
$stmt->execute();

$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

if (empty($rows)) {
    header('HTTP/1.0 404 Not Found');
    echo json_encode(["respuesta" => "No se encontraron registros."]);
    exit;
}

// Concatenar grabaciones en un solo campo si hay más de un registro
if (count($rows) > 1) {
    $mensajes = [];
    foreach ($rows as $index => $registro) {
        $num = $index + 1;
        $redaccion = trim($registro['grabacion']);
        $mensajes[] = "Mensaje {$num}: {$redaccion}";
    }
    // Tomamos el primer registro como base y sobreescribimos grabacion
    $data = $rows[0];
    $data['grabacion'] = implode(", ", $mensajes);
    $data['grabacion2'] = implode(", ", $mensajes);
} else {
    // Solo un registro, devolver tal cual
    $data = $rows[0];
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
?>