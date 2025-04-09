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
    cyc.status_cyc,
    cyc.fecha_programacion,
    u.nombre_usuario,
    cyc.redaccion_canales,
    cyc.proyecto
FROM [contingencias].[dbo].[cyc] AS cyc
INNER JOIN [usuarios] AS u ON cyc.id_usuario = u.idUsuarios
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
        $messages = [];
        
        // Procesar cada registro y construir el mensaje
        foreach ($resultado as $row) {
            // Cambiar el valor de redaccion_cyc por la cadena compuesta
            $message = $row['tipo_cyc'] . ' Registrada ' . $row['redaccion_cyc'] . ' ' . $row['nombre'] . " con el numero de ticket " . $row['no_ticket'];

            // Crear el formato de la respuesta
            $record = [
                "id_cyc" => $row['id_cyc'],
                "nombre" => $row['nombre'],
                "no_ticket" => $row['no_ticket'],
                "nombre_crisis" => $row['nombre'],
                "criticidad" => $row['criticidad'],
                "tipo_cyc" => $row['tipo_cyc'],
                "ubicacion_cyc" => $row['ubicacion_cyc'],
                "redaccion_cyc" => $message, // Cambiar el valor de redaccion_cyc
                "canal_cyc" => json_decode($row['canal_cyc'], true),  // Asegurar que esté en formato de array
                "bot_cyc" => json_decode($row['bot_cyc'], true),  // Asegurar que esté en formato de array
                "fecha_registro_cyc" => $row['fecha_registro_cyc'],
                "status_cyc" => $row['status_cyc'],
                "fecha_programacion" => $row['fecha_programacion'],
                "nombre_usuario" => $row['nombre_usuario'],
                "redaccion_canales" => $row['redaccion_canales'],
                "proyecto" => $row['proyecto']
            ];
            
            $messages[] = $record; // Agregar al array de mensajes
        }

        // Retornar la respuesta en formato JSON
        echo json_encode($messages);
    }
} catch (PDOException $e) {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(["error" => "Error al ejecutar la consulta.", "details" => $e->getMessage()]);
}

// Cerrar la conexión
$conn = null;
?>
