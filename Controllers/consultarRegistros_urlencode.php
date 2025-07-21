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

// Leer los parámetros de la URL
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

// Consulta SQL segura con parámetros
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
FROM [contingencias].[dbo].[cyc] AS cyc
INNER JOIN [usuarios] AS u ON cyc.id_usuario = u.idUsuarios
WHERE cyc.proyecto = ? AND cyc.ubicacion_cyc = ? AND cyc.status_cyc = 1;
";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([$proyecto, $ubicacion]);

    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($resultado)) {
        header('HTTP/1.0 200 Ok');
        echo json_encode(["status_cyc" => "Inactivo"]);
    } else {
        $messages = [];

        foreach ($resultado as $row) {
            $message = $row['tipo_cyc'] . ' Registrada ' . $row['redaccion_cyc'] . ' ' . $row['nombre'] . " con el número de ticket " . $row['no_ticket'];

            $record = [
                "id_cyc" => $row['id_cyc'],
                "nombre" => $row['nombre'],
                "no_ticket" => $row['no_ticket'],
                "nombre_crisis" => $row['nombre'],
                // "criticidad" => $row['criticidad'], // Comentado: no existe en SELECT
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

        echo json_encode($messages);
    }
} catch (PDOException $e) {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(["error" => "Error al ejecutar la consulta.", "details" => $e->getMessage()]);
}

$conn = null;
?>
