<?php
header('Content-Type: application/json');

// Confirmar método GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Recuperar los parámetros
    $proyecto = $_GET['proyecto'] ?? null;
    $ubicacion = $_GET['ubicacion'] ?? null;

    // Retornar JSON con los datos
    echo json_encode([
        'proyecto' => $proyecto,
        'ubicacion' => $ubicacion
    ]);
    exit;
} else {
    // Método no permitido
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Usa GET.']);
    exit;
}
