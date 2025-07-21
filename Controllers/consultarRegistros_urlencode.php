<?php
session_start();
include("../Controllers/bd.php");
header('Content-Type: application/json');

// Validar autenticación básica
$valid_user = "Admin_fanafesa";
$valid_password = "F4n4f3s4_2025";

if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) || 
    $_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_password) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(["error" => "Credenciales inválidas."]);
    exit;
}

// Validar sesión proyecto
if (!isset($_SESSION['proyecto']) || !is_numeric($_SESSION['proyecto'])) {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(["error" => "La variable de sesión 'proyecto' no está definida o no es válida."]);
    exit;
}
$proyecto = intval($_SESSION['proyecto']);

// Validar parámetro "ubicacion"
if (!isset($_GET['ubicacion']) || !is_numeric($_GET['ubicacion'])) {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(["error" => "El parámetro 'ubicacion' es obligatorio y debe ser un número."]);
    exit;
}
$ubicacion = intval($_GET['ubicacion']);

// Consulta SQL con los joins y todos los campos necesarios
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
FROM contingencias.dbo.cyc AS cyc
INNER JOIN usuarios AS u ON cyc.id_usuario = u.idUsuarios
WHERE cyc.proyecto = ? AND cyc.ubicacion_cyc = ?
AND cyc.status_cyc = 1;
";

if ($stmt = $conn->prepare($sql)) {
    // "ii" indica que los parámetros son enteros (int, int)
    $stmt->bind_param("ii", $proyecto, $ubicacion);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header('HTTP/1.0 200 OK');
            echo json_encode(["status_cyc" => "Inactivo"]);
        } else {
            $messages = [];

            while ($row = $result->fetch_assoc()) {
                $message = $row['tipo_cyc'] . ' Registrada ' . $row['redaccion_cyc'] . ' ' . $row['nombre'] . " con el numero de ticket " . $row['no_ticket'];

                $record = [
                    "id_cyc" => $row['id_cyc'],
                    "nombre" => $row['nombre'],
                    "no_ticket" => $row['no_ticket'],
                    "nombre_crisis" => $row['nombre'],       // campo original 'nombre_crisis' no está en esta consulta
                    "criticidad" => $row['criticidad'] ?? null, // puede que no exista
                    "tipo_cyc" => $row['tipo_cyc'],
                    "ubicacion_cyc" => $row['ubicacion_cyc'],
                    "grabacion" => $message,
                    "canal_cyc" => json_decode($row['canal_cyc'], true),
                    "bot_cyc" => json_decode($row['bot_cyc'], true),
                    "fecha_registro_cyc" => $row['fecha_registro_cyc'],
                    "status_cyc" => $row['status_cyc'],
                    "fecha_programacion" => $row['fecha_programacion'],
                    "nombre_usuario" => $row['nombre_usuario'],
                    "redaccion_canales" => $row['redaccion_canales'],
                    "proyecto" => $row['proyecto']
                ];

                $messages[] = $record;
            }

            echo json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $result->free();
    } else {
        header('HTTP/1.0 500 Internal Server Error');
        echo json_encode(["error" => "Error al ejecutar la consulta.", "details" => $stmt->error]);
    }

    $stmt->close();
} else {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(["error" => "Error al preparar la consulta.", "details" => $conn->error]);
}

$conn->close();
?>
