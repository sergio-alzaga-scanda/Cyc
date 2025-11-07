<?php
include("../Controllers/bd.php");

header('Content-Type: application/json'); // Muy importante

if (!isset($_GET['proyecto'])) {
    echo json_encode(['error' => 'No se recibió proyecto']);
    exit;
}

$proyecto_id = $_GET['proyecto'];

$stmt = $conn->prepare("SELECT id_ubicacion_ivr, nombre_ubicacion_ivr FROM ubicacion_ivr WHERE status > 0 AND proyecto = ?");
if (!$stmt) {
    echo json_encode(['error' => 'Error en prepare: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $proyecto_id);

if (!$stmt->execute()) {
    echo json_encode(['error' => 'Error en execute: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$ubicaciones = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($ubicaciones);

$stmt->close();
$conn->close();
