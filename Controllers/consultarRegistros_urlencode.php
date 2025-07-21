<?php
// Establecer encabezados para permitir acceso cruzado y respuesta en JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Validar que el método sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Método no permitido
    echo json_encode(["error" => "Método no permitido. Solo se permite GET."]);
    exit();
}

// Verificar que se recibieron los parámetros necesarios
if (!isset($_GET['proyecto']) || !isset($_GET['ubicacion'])) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["error" => "Faltan parámetros. Se requieren 'proyecto' y 'ubicacion'."]);
    exit();
}

// Obtener los parámetros desde la URL
$proyecto = $_GET['proyecto'];
$ubicacion = $_GET['ubicacion'];

// Preparar y enviar la respuesta
$response = [
    "proyecto" => $proyecto,
    "ubicacion" => $ubicacion,
    "mensaje" => "Datos recibidos correctamente"
];

echo json_encode($response);
