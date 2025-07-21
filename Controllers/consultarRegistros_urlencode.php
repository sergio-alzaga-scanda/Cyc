<?php
header('Content-Type: application/json');

// Log para saber qué método se está usando
error_log('Método usado: ' . $_SERVER['REQUEST_METHOD']);

// Mostrar variables GET recibidas (para depuración)
error_log('$_GET completo: ' . print_r($_GET, true));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Usamos el operador null coalescing
    $proyecto = $_GET['proyecto'] ?? null;
    $ubicacion = $_GET['ubicacion'] ?? null;

    // Log de valores individuales
    error_log("proyecto: [$proyecto], ubicacion: [$ubicacion]");

    echo json_encode([
        'proyecto' => $proyecto,
        'ubicacion' => $ubicacion
    ]);
    exit;
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Usa GET.']);
    exit;
}
?>
