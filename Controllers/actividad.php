<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

include("../Controllers/bd.php");

$id_usuario     = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_usuario'];
$proyecto       = $_SESSION['proyecto']; // Proyecto desde la sesión

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

switch ($accion) {
    case 1:
        $DtosTbl = [];

        // Consulta filtrando por el campo "proyecto"
        $query = "SELECT fecha, user_id, name_user, description FROM logs WHERE proyecto = ? ORDER BY fecha DESC";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            echo json_encode(['error' => $conn->error]);
            exit;
        }

        $stmt->bind_param("s", $proyecto);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $fechaOriginal = $row['fecha'];
            $fechaFormateada = 'Fecha inválida';

            if ($fechaOriginal) {
                $fechaSinMs = substr($fechaOriginal, 0, 19); // Elimina milisegundos
                $dateObj = DateTime::createFromFormat('Y-m-d H:i:s', $fechaSinMs);
                if ($dateObj) {
                    $fechaFormateada = $dateObj->format('d-m-Y H:i');
                }
            }

            $DtosTbl[] = [
                'fecha'       => $fechaFormateada,
                'user_id'     => $row['user_id'],
                'name_user'   => $row['name_user'],
                'description' => $row['description'],
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($DtosTbl);
        break;

    default:
        echo "Acción no reconocida.";
}
?>
