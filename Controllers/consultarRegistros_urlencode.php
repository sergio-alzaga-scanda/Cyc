<?php
// Encabezados
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Solo se permite GET."]);
    exit();
}

// Validar parámetros
if (!isset($_GET['proyecto']) || !isset($_GET['ubicacion'])) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan parámetros. Se requieren 'proyecto' y 'ubicacion'."]);
    exit();
}

// Obtener parámetros
$proyecto = $_GET['proyecto'];
$ubicacion = $_GET['ubicacion'];

// Credenciales de conexión
$servername = "localhost";
$port       = 3306;
$username   = "root";
$password   = "Melco154.,";
$database   = "Cyc";

try {
    $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta preparada
    $stmt = $pdo->prepare("
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
    ");

    $stmt->execute([$proyecto, $ubicacion]);

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response = [
            "message" => $row['tipo_cyc'] . ' Registrada ' . $row['redaccion_cyc'] . ' ' . $row['nombre'] . " con el número de ticket " . $row['no_ticket'],
            "status_cyc" => $row['status_cyc']
        ];
    } else {
        $response = [
            "message" => "No se encontraron registros con ese proyecto y ubicación",
            "status_cyc" => null
        ];
    }

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
}
