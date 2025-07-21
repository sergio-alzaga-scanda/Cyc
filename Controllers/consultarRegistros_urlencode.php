<?php
header('Content-Type: application/json');

// Detectar método de solicitud
$method = $_SERVER['REQUEST_METHOD'];
error_log('Método usado: ' . $method);

// Inicializar variables
$proyecto = null;
$ubicacion = null;

if ($method === 'GET') {
    // Obtener parámetros de la URL
    $proyecto = $_GET['proyecto'] ?? null;
    $ubicacion = $_GET['ubicacion'] ?? null;

    error_log("GET - proyecto: [$proyecto], ubicacion: [$ubicacion]");
} elseif ($method === 'POST') {
    // Obtener parámetros del cuerpo de la solicitud
    $proyecto = $_POST['proyecto'] ?? null;
    $ubicacion = $_POST['ubicacion'] ?? null;

    error_log("POST - proyecto: [$proyecto], ubicacion: [$ubicacion]");
} else {
    // Método no permitido
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Usa GET o POST.']);
    exit;
}

// Devolver respuesta JSON
echo json_encode([
    'proyecto' => $proyecto,
    'ubicacion' => $ubicacion
]);
exit;
?>
