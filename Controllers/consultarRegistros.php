<?php
session_start();
include("../Controllers/bd.php");
header('Content-Type: application/json');

// Validar autenticación básica
$valid_user     = "Admin_fanafesa";
$valid_password = "F4n4f3s4_2025";

if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) || 
    $_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_password) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(["error" => "Credenciales inválidas."]);
    exit;
}

// Validar que exista la sesión con proyecto
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

// --- FUNCIÓN PARA LIMPIAR SALTOS DE LÍNEA ---
function limpiarGrabacion($texto) {
    $texto = str_replace(["\n", "\r"], '', $texto);
    return trim($texto);
}

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
FROM contingencias.dbo.cyc AS cyc
LEFT JOIN contingencias.dbo.cat_crisis AS cat_crisis
    ON cyc.categoria_cyc = cat_crisis.id
LEFT JOIN contingencias.dbo.ubicacion_ivr AS ubicaciones
    ON cyc.ubicacion_cyc = ubicaciones.id_ubicacion_ivr
LEFT JOIN contingencias.dbo.usuarios AS usuarios
    ON cyc.id_usuario = usuarios.idUsuarios
WHERE cyc.proyecto = ? AND cyc.ubicacion_cyc = ?
AND cyc.status_cyc = 1;
";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $proyecto, $ubicacion);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header('HTTP/1.0 404 Not Found');
            echo json_encode(["respuesta" => "No se encontraron registros."]);
        } else {
            $resultado = [];
            while ($row = $result->fetch_assoc()) {
                // Limpiar saltos de línea en el campo redaccion_cyc
                if (isset($row['redaccion_cyc'])) {
                    $row['redaccion_cyc'] = limpiarGrabacion($row['redaccion_cyc']);
                }
                $resultado[] = $row;
            }

            echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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

// Cerrar la conexión
$conn->close();
?>
