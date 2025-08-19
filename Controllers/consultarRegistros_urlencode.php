<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../Controllers/bd.php");
header('Content-Type: application/json');

// Validar autenticación básica
$valid_user     = "Admin_fanafesa";
$valid_password = "F4n4f3s4_2025";

if ($_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_password) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(["error" => "Credenciales inválidas."]);
    exit;
}

// Validar los parámetros "proyecto" y "ubicacion"
if (!isset($_GET['proyecto']) || !is_numeric($_GET['proyecto'])) {
    header('HTTP/1.0 400');
    echo json_encode(["error" => "El parámetro 'proyecto' es obligatorio y debe ser un número."]);
    exit;
}

if (!isset($_GET['ubicacion']) || !is_numeric($_GET['ubicacion'])) {
    header('HTTP/1.0 400');
    echo json_encode(["error" => "El parámetro 'ubicacion' es obligatorio y debe ser un número."]);
    exit;
}

$proyecto  = intval($_GET['proyecto']);
$ubicacion = intval($_GET['ubicacion']);

// Consulta SQL con los joins
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
    cyc.redaccion_cyc,
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
FROM contingencias.cyc AS cyc
LEFT JOIN contingencias.cat_crisis AS cat_crisis
    ON cyc.categoria_cyc = cat_crisis.id
LEFT JOIN contingencias.ubicacion_ivr AS ubicaciones
    ON cyc.ubicacion_cyc = ubicaciones.id_ubicacion_ivr
LEFT JOIN contingencias.usuarios AS usuarios
    ON cyc.id_usuario = usuarios.idUsuarios
WHERE cyc.proyecto = ? AND cyc.ubicacion_cyc = ?
AND cyc.status_cyc = 1;
";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([$proyecto, $ubicacion]);

    // Construir el resultado
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si se encontraron registros
    if (empty($resultado)) {
        header('HTTP/1.0 404 Not Found');
        echo json_encode(["respuesta" => "No se encontraron registros."]);
    } else {
        // Retornar la respuesta en formato JSON
        echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(["error" => "Error al ejecutar la consulta.", "details" => $e->getMessage()]);
}

// Cerrar la conexión
$conn = null;