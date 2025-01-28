<?php
include("../Controllers/bd.php");
header('Content-Type: application/json');



// Validar autenticación básica
$valid_user = "Admin_fanafesa";
$valid_password = "F4n4f3s4_2025";

if ($_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_password) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(["error" => "Credenciales inválidas."]);
    exit;
}

// Validar los parámetros "proyecto" y "ubicacion"
if (!isset($_GET['proyecto']) || !is_numeric($_GET['proyecto'])) {
    echo json_encode(["error" => "El parámetro 'proyecto' es obligatorio y debe ser un número."]);
    exit;
}

if (!isset($_GET['ubicacion']) || !is_numeric($_GET['ubicacion'])) {
    echo json_encode(["error" => "El parámetro 'ubicacion' es obligatorio y debe ser un número."]);
    exit;
}

$proyecto = intval($_GET['proyecto']);
$ubicacion = intval($_GET['ubicacion']);

// Consulta SQL con los joins
$sql = "
SELECT
    cyc.id_cyc,
    cyc.nombre,
    cyc.no_ticket,
    cyc.categoria_cyc,
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
    cyc.status_cyc,
    cyc.fecha_programacion,
    cyc.id_usuario,
    cyc.redaccion_canales,
    cyc.proyecto
FROM [contingencias].[dbo].[cyc] AS cyc
LEFT JOIN [contingencias].[dbo].[cat_crisis] AS cat_crisis
    ON cyc.categoria_cyc = cat_crisis.id
LEFT JOIN [contingencias].[dbo].[ubicacion_ivr] AS ubicaciones
    ON cyc.ubicacion_cyc = ubicaciones.id_ubicacion_ivr
WHERE cyc.proyecto = ? AND cyc.ubicacion_cyc = ?
AND cyc.status_cyc = 1
";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([$proyecto, $ubicacion]);

    // Construir el resultado
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornar la respuesta en formato JSON
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al ejecutar la consulta.", "details" => $e->getMessage()]);
}

// Cerrar la conexión
$conn = null;
