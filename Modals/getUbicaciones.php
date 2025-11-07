<?php
include("../Controllers/bd.php");

if (!isset($_GET['proyecto'])) {
    echo json_encode([]);
    exit;
}

$proyecto_id = $_GET['proyecto'];

$stmt = $conn->prepare("SELECT id_ubicacion_ivr, nombre_ubicacion_ivr FROM ubicacion_ivr WHERE status >= 1 AND proyecto = ?");
$stmt->bind_param("s", $proyecto_id);
$stmt->execute();
$result = $stmt->get_result();
$ubicaciones = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($ubicaciones);
$stmt->close();
$conn->close();
?>
