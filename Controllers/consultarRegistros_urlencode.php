<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../Controllers/bd.php"); // Asegúrate que aquí $conn es mysqli
header('Content-Type: application/json');

$valid_user = "Admin_fanafesa";
$valid_password = "F4n4f3s4_2025";

// Validar autenticación básica
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
WHERE cyc.proyecto = 2 AND cyc.ubicacion_cyc = 2 AND cyc.status_cyc = 1
";

if ($stmt = $conn->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();

    $resultado = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($resultado)) {
        http_response_code(200);
        echo json_encode(["status_cyc" => "Inactivo"]);
        exit;
    }

    $messages = [];
    foreach ($resultado as $row) {
        $message = $row['tipo_cyc'] . ' Registrada ' . $row['redaccion_cyc'] . ' ' . $row['nombre'] . " con el número de ticket " . $row['no_ticket'];

        $record = [
            "id_cyc" => $row['id_cyc'],
            "nombre" => $row['nombre'],
            "no_ticket" => $row['no_ticket'],
            "nombre_crisis" => $row['nombre'],
            //"criticidad" => $row['criticidad'], // Asegúrate que exista
            "tipo_cyc" => $row['tipo_cyc'],
            "ubicacion_cyc" => $row['ubicacion_cyc'],
            "grabacion" => $message,
            "canal_cyc" => json_decode($row['canal_cyc'], true) ?? [],
            "bot_cyc" => json_decode($row['bot_cyc'], true) ?? [],
            "fecha_registro_cyc" => $row['fecha_registro_cyc'],
            "status_cyc" => $row['status_cyc'],
            "fecha_programacion" => $row['fecha_programacion'],
            "nombre_usuario" => $row['nombre_usuario'],
            "redaccion_canales" => $row['redaccion_canales'],
            "proyecto" => $row['proyecto']
        ];
        $messages[] = $record;
    }

    echo json_encode($record);

    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error en la preparación de la consulta.", "details" => $conn->error]);
}

$conn->close();
?>
