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

// Leer los datos del cuerpo de la solicitud (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Validar los parámetros "proyecto" y "ubicacion"
if (!isset($data['proyecto']) || !is_numeric($data['proyecto'])) {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(["error" => "El parámetro 'proyecto' es obligatorio y debe ser un número."]);
    exit;
}

if (!isset($data['ubicacion']) || !is_numeric($data['ubicacion'])) {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(["error" => "El parámetro 'ubicacion' es obligatorio y debe ser un número."]);
    exit;
}

$proyecto = intval($data['proyecto']);
$ubicacion = intval($data['ubicacion']);

// Consulta SQL con los joins
$sql = "
SELECT
    cyc.id_cyc,
    CASE cyc.tipo_cyc 
        WHEN 1 THEN 'Crisis'
        WHEN 2 THEN 'Contingencia'
        ELSE 'Desconocido'
    END AS tipo_cyc,
    cyc.nombre,
    cyc.no_ticket,
    cyc.redaccion_cyc as grabacion
FROM [contingencias].[dbo].[cyc] AS cyc
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
        
        // Concatenar los mensajes para cada registro
        foreach ($resultado as $row) {
            $message = $row['tipo_cyc']. ' Registrada '. $row['grabacion'] .' '.$row['nombre']. " con el numero de ticket " . $row['no_ticket'];
            $messages[] = $message;
        }
        
        // Si hay más de un registro, agregar "y" antes del último
        if (count($messages) > 1) {
            $lastMessage = array_pop($messages); // Eliminar el último mensaje
            $result = implode(", ", $messages) . " y " . $lastMessage;
        } else {
            $result = $messages[0]; // Solo un mensaje
        }

        // Retornar la respuesta en formato JSON
        echo json_encode(["respuesta" => $result]);
    }
} catch (PDOException $e) {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(["error" => "Error al ejecutar la consulta.", "details" => $e->getMessage()]);
}

// Cerrar la conexión
$conn = null;
?>
