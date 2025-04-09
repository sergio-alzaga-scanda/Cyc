<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
}
include("../Controllers/bd.php");
$id_usuario     = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_usuario'];
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fechaActual = date("Y-m-d H:i:s");
$fechaHoraActual = $fechaActual;

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;


switch ($accion) {
	case 1:
    $DtosTbl = array();

    try {
        // Definir la nueva consulta
        $queryTbl = "SELECT fecha, user_id, name_user, description FROM logs ORDER BY fecha DESC;";

        // Ejecutar la consulta usando PDO
        $stmt = $conn->query($queryTbl);

        if ($stmt === false) {
            echo json_encode(['error' => $conn->errorInfo()]);
            exit;
        }

        // Obtener los resultados y prepararlos
        while ($rowTbl = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Formatear la fecha_activacion para datetime-local
            if ($rowTbl['fecha']) {
                // Quitar la parte de los milisegundos
                $fechaSinMilisegundos = substr($rowTbl['fecha'], 0, 19); // '2025-01-21 16:55:00'

                // Intentar crear el objeto DateTime con el formato adecuado
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $fechaSinMilisegundos);
                if ($date) {
                    $rowTbl['fecha'] = $date->format('d-m-Y H:i'); // Formato para datetime-local
                } else {
                    // Si el formato es inválido
                    $rowTbl['fecha'] = 'Fecha inválida';
                }
            }

            $DtosTbl[] = array(
				//'id'          => $rowTbl['id'],
				'fecha'       => $rowTbl['fecha'],
				'user_id'     => $rowTbl['user_id'],
				'name_user'   => $rowTbl['name_user'], 
				'description' => $rowTbl['description']
            );
        }

        // Enviar la respuesta como JSON
        header('Content-Type: application/json');
        echo json_encode($DtosTbl);
    } catch (Exception $e) {
        // Capturar cualquier error y devolverlo como JSON
        echo json_encode(['error' => $e->getMessage()]);
    }
    break;
	
	default:
    	echo "Acción no reconocida.";
}