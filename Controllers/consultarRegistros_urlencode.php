<?php
// Establecer el encabezado de respuesta como JSON
header('Content-Type: application/json');

// Verificar que la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener parámetros desde la URL
    $proyecto = isset($_GET['proyecto']) ? $_GET['proyecto'] : null;
    $ubicacion = isset($_GET['ubicacion']) ? $_GET['ubicacion'] : null;

    // Devolver los datos como JSON
    echo json_encode([
        'proyecto' => $proyecto,
        'ubicacion' => $ubicacion
    ]);
} else {
    // Si no es GET, devolver un error
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'error' => 'Método no permitido. Usa GET.'
    ]);
}
?>
