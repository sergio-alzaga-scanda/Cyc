<?php
header("Content-Type: application/json; charset=UTF-8");

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed."]);
    exit();
}

// Leer parámetros desde $_POST
$proyecto = $_POST['proyecto'] ?? null;
$ubicacion = $_POST['ubicacion'] ?? null;

if ($proyecto === null || $ubicacion === null) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields: 'proyecto' and 'ubicacion'."]);
    exit();
}

// Datos de conexión
$servername = "localhost";
$port       = 3306;
$username   = "root";
$password   = "Melco154.,";
$database   = "Cyc";

try {
    $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $query = "
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
            CASE cyc.status_cyc 
                WHEN '1' THEN 'Activo'
                WHEN '0' THEN 'Desactivado'
                ELSE 'Desconocido'
            END AS status_cyc,
            cyc.fecha_programacion,
            u.nombre_usuario,
            cyc.redaccion_canales,
            cyc.proyecto
        FROM cyc
        INNER JOIN usuarios AS u ON cyc.id_usuario = u.idUsuarios
        WHERE cyc.proyecto = ? AND cyc.ubicacion_cyc = ? AND cyc.status_cyc = 1
        LIMIT 1
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$proyecto, $ubicacion]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
       $mensaje=  "{$row['tipo_cyc']} Registrada {$row['redaccion_cyc']} {$row['nombre']} con el número de ticket {$row['no_ticket']}";
        $response = [
            "grabacion" => "{$row['tipo_cyc']} Registrada {$row['redaccion_cyc']} {$row['nombre']} con el número de ticket {$row['no_ticket']}",
            "status_cyc" => $row['status_cyc']
        ];
        echo ($mensaje);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "No records found for the given proyecto and ubicacion."]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
