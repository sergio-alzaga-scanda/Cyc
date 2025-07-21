<?php
header("Content-Type: application/json; charset=UTF-8");

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Invalid method"]);
    exit();
}

// Leer el cuerpo crudo y decodificar JSON
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Validar que se haya recibido JSON válido
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit();
}

// Obtener parámetros desde el JSON
$proyecto = $data['proyecto'] ?? null;
$ubicacion = $data['ubicacion'] ?? null;

// Validar parámetros
if (!$proyecto || !$ubicacion) {
    http_response_code(400);
    echo json_encode(["error" => "Missing proyecto or ubicacion"]);
    exit();
}

// Conexión a base de datos
try {
    $pdo = new PDO(
        "mysql:host=localhost;port=3306;dbname=Cyc;charset=utf8",
        "root", "Melco154.,",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare("
        SELECT
            CASE tipo_cyc WHEN 1 THEN 'Crisis' WHEN 2 THEN 'Contingencia' ELSE 'Desconocido' END AS tipo,
            redaccion_cyc AS texto,
            no_ticket AS ticket,
            CASE status_cyc WHEN '1' THEN 'Activo' WHEN '0' THEN 'Desactivado' ELSE 'Desconocido' END AS status
        FROM cyc
        WHERE proyecto = ? AND ubicacion_cyc = ? AND status_cyc = 1
        LIMIT 1
    ");
    $stmt->execute([$proyecto, $ubicacion]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "No data"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB error"]);
}