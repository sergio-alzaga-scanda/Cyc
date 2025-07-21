<?php
// Configuración de autenticación
$valid_user = "Admin_fanafesa";
$valid_password = "F4n4f3s4_2025";

// Autenticación HTTP básica
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_password) {
    header('WWW-Authenticate: Basic realm="API Access"');
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit;
}

// Validar método GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Obtener parámetros
$proyecto = isset($_GET['proyecto']) ? $_GET['proyecto'] : null;
$ubicacion = isset($_GET['ubicacion']) ? $_GET['ubicacion'] : null;

if (!$proyecto || !$ubicacion) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Faltan parámetros requeridos"]);
    exit;
}

// Conexión a la base de datos (ajusta tus valores)
$host = 'localhost';
$db = 'tu_basedatos';
$user = 'tu_usuario';
$pass = 'tu_contraseña';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

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
        FROM `cyc` AS cyc
        INNER JOIN `usuarios` AS u ON cyc.id_usuario = u.idUsuarios
        WHERE cyc.proyecto = ? AND cyc.ubicacion_cyc = ? AND cyc.status_cyc = 1
    ");

    $stmt->execute([$proyecto, $ubicacion]);

    $results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            "message" => $row['tipo_cyc'] . ' Registrada ' . $row['redaccion_cyc'] . ' ' . $row['nombre'] . " con el número de ticket " . $row['no_ticket'],
            "status_cyc" => $row['status_cyc']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($results);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
}
?>
