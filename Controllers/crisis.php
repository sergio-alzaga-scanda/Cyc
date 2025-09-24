<?php
date_default_timezone_set('America/Mexico_City');

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php");

$id_usuario      = $_SESSION['usuario'];
$nombre_usuario  = $_SESSION['nombre_usuario'];
$proyecto        = $_SESSION['proyecto'] ?? '';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

switch ($accion) {

case 1: // Crear o registrar un ticket
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Establece la zona horaria adecuada
        date_default_timezone_set('America/Mexico_City'); // Cambia según tu región

        $fecha = $_POST['fecha_programacion'] ?? null;

        if ($fecha) {
            $status = 3;
            $fecha_string     = trim($fecha);
            try {
                $fecha_obj          = new DateTime($fecha_string);
                $fecha_programacion = $fecha_obj->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                echo "Error al procesar la fecha: " . $e->getMessage();
                exit;
            }
        } else {
            $status = 1;
            $fecha_programacion = null;
        }

        // Obtener la fecha de registro con la zona horaria correcta
        $fecha_registro = date('Y-m-d H:i:s');

        $no_ticket           = $_POST['no_ticket'] ?? '';
        $nombre              = $_POST['nombre'] ?? '';
        $criticidad          = $_POST['criticidad'] ?? '';
        $tipo                = $_POST['tipo'] ?? '';
        $ubicacion           = $_POST['ubicacion'] ?? '';
        $ivr_texto           = $_POST['ivr'] ?? '';
        $redaccion_canales   = $_POST['redaccion_canales'] ?? '';
        $canales             = $_POST['canal'] ?? [];
        $bots                = $_POST['bot'] ?? [];
        $canal_digital_texto = $_POST['canal-digital-texto'] ?? '';
        $canales_json        = json_encode($canales);
        $bots_json           = json_encode($bots);

        $query_check = "SELECT COUNT(*) FROM cyc WHERE no_ticket = ? AND status_cyc IN (1, 3) AND proyecto = ?";

        if ($stmt_check = $conn->prepare($query_check)) {
            $stmt_check->bind_param("ss", $no_ticket, $proyecto);
            $stmt_check->execute();
            $stmt_check->bind_result($ticket_exists);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($ticket_exists > 0) {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script>
                        window.onload = () => {
                            Swal.fire({
                                title: 'Error',
                                text: 'El número de ticket ya existe.',
                                icon: 'error',
                                confirmButtonText: 'Cerrar'
                            }).then(() => window.history.back());
                        }
                      </script>";
                exit;
            } else {
                $query = "INSERT INTO cyc (
                            nombre, no_ticket, categoria_cyc, tipo_cyc, ubicacion_cyc, redaccion_cyc,
                            canal_cyc, bot_cyc, redaccion_canal_cyc, fecha_registro_cyc, status_cyc,
                            fecha_programacion, id_usuario, redaccion_canales, proyecto
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param(
                        "ssiiissssssiss",
                        $nombre,
                        $no_ticket,
                        $criticidad,
                        $tipo,
                        $ubicacion,
                        $ivr_texto,
                        $canales_json,
                        $bots_json,
                        $canal_digital_texto,
                        $fecha_registro,          // <--- fecha local desde PHP
                        $status,
                        $fecha_programacion,
                        $id_usuario,
                        $redaccion_canales,
                        $proyecto
                    );

                    if ($stmt->execute()) {
                        $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto)
                                     VALUES (?, ?, ?, ?, ?)";

                        if ($stmtLog = $conn->prepare($queryLog)) {
                            $descripcion = 'El ticket se ha registrado correctamente, numero de ticket: ' . $no_ticket;
                            $fecha_log   = date('Y-m-d H:i:s'); // También fecha local
                            $stmtLog->bind_param("sisss", $fecha_log, $id_usuario, $nombre_usuario, $descripcion, $proyecto);
                            $stmtLog->execute();
                            $stmtLog->close();
                        }

                        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script>
                                window.onload = () => {
                                    Swal.fire({
                                        title: 'Éxito',
                                        text: 'El ticket se ha registrado correctamente.',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    }).then(() => window.location.href = '../Views/cyc.php');
                                }
                              </script>";
                    } else {
                        echo "Error al insertar el registro: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo "Error en la preparación de la consulta: " . $conn->error;
                }
            }
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        }
    }
    break;

    case 2: // Obtener tabla de tickets
        $DtosTbl = [];
        $queryTbl = "
            SELECT 
                c.id_cyc, c.no_ticket, c.status_cyc, cc.nombre_crisis AS categoria_nombre,
                CASE WHEN c.tipo_cyc = 1 THEN 'Crisis' WHEN c.tipo_cyc = 2 THEN 'Contingencia' ELSE 'Desconocido' END AS tipo_cyc,
                c.ubicacion_cyc, ui.nombre_ubicacion_ivr AS nombre_ubicacion,
                CASE 
                    WHEN c.status_cyc = 3 AND c.fecha_programacion IS NOT NULL THEN c.fecha_programacion
                    ELSE c.fecha_registro_cyc
                END AS fecha_activacion,
                p.nombre_proyecto
            FROM cyc AS c
            JOIN cat_crisis AS cc ON c.categoria_cyc = cc.id
            LEFT JOIN ubicacion_ivr AS ui ON c.ubicacion_cyc = ui.id_ubicacion_ivr
            LEFT JOIN cat_proyectos AS p ON c.proyecto = p.id_proyecto
            WHERE c.status_cyc > 0 AND c.proyecto = ?
            ORDER BY c.fecha_registro_cyc DESC;
        ";

        if ($stmt = $conn->prepare($queryTbl)) {
            $stmt->bind_param("s", $proyecto);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($rowTbl = $result->fetch_assoc()) {
                if ($rowTbl['fecha_activacion']) {
                    $date = DateTime::createFromFormat('Y-m-d H:i:s', substr($rowTbl['fecha_activacion'], 0, 19));
                    $rowTbl['fecha_activacion'] = $date ? $date->format('d-m-Y H:i') : 'Fecha inválida';
                }
                $DtosTbl[] = $rowTbl;
            }
            $stmt->close();
            header('Content-Type: application/json');
            echo json_encode($DtosTbl);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;

    case 3: // Obtener datos de un ticket para editar
        $id_cyc = $_POST['id_cyc'] ?? $_POST['id'] ?? 0;
        $proyecto = $_SESSION['proyecto'] ?? $_POST['proyecto'] ?? null;
        $query = "SELECT c.*, cc.nombre_crisis, ui.nombre_ubicacion_ivr, p.nombre_proyecto FROM cyc AS c LEFT JOIN cat_crisis AS cc ON c.categoria_cyc = cc.id LEFT JOIN ubicacion_ivr AS ui ON c.ubicacion_cyc = ui.id_ubicacion_ivr LEFT JOIN cat_proyectos AS p ON c.proyecto = p.id_proyecto WHERE c.id_cyc = ? AND c.proyecto = ? LIMIT 1";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("is", $id_cyc, $proyecto);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $row['canal_cyc'] = json_decode($row['canal_cyc'] ?? '[]', true);
                $row['bot_cyc'] = json_decode($row['bot_cyc'] ?? '[]', true);
                $result = $row;
            } else {
                $result = ['error' => 'No se encontró el ticket.'];
            }
            $stmt->close();
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Error en prepare: ' . $conn->error]);
        }
        break;

    case 4: // Editar ticket
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $fecha = $_POST['fecha_programacion'] ?? null;
            
            if ($fecha) {
                $status = 3;
                $fecha_string = trim($fecha);
                try {
                    $fecha_obj = new DateTime($fecha_string);
                    $fecha_programacion = $fecha_obj->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    echo "Error al procesar la fecha: " . $e->getMessage();
                    exit;
                }
            } else {
                $status = 1;
                $fecha_programacion = null;
            }

            $id_cyc              = $_POST['id'] ?? 0;
            $no_ticket           = $_POST['no_ticket'] ?? '';
            $nombre              = $_POST['nombre'] ?? '';
            $criticidad          = $_POST['criticidad'] ?? '';
            $tipo                = $_POST['tipo'] ?? '';
            $ubicacion           = $_POST['ubicacion'] ?? '';
            $ivr_texto           = $_POST['ivr'] ?? '';
            $redaccion_canales   = $_POST['redaccion_canales'] ?? '';
            $canales             = $_POST['canal'] ?? [];
            $bots                = $_POST['bot'] ?? [];
            $canal_digital_texto = $_POST['canal-digital-texto'] ?? '';
            $canales_json = json_encode($canales);
            $bots_json    = json_encode($bots);
            $query = "UPDATE cyc SET nombre = ?, no_ticket = ?, categoria_cyc = ?, tipo_cyc = ?, ubicacion_cyc = ?, redaccion_cyc = ?, canal_cyc = ?, bot_cyc = ?, redaccion_canal_cyc = ?, status_cyc = ?, fecha_programacion = ?, id_usuario = ?, redaccion_canales = ? WHERE id_cyc = ? AND proyecto = ?";
            if ($stmt = $conn->prepare($query)) {
                
                // --- LÍNEA CORREGIDA ---
                // El 11vo caracter era 'i' (integer) y se cambió a 's' (string) para la fecha.
                $stmt->bind_param("ssiiisssssssssi", $nombre, $no_ticket, $criticidad, $tipo, $ubicacion, $ivr_texto, $canales_json, $bots_json, $canal_digital_texto, $status, $fecha_programacion, $id_usuario, $redaccion_canales, $id_cyc, $proyecto);
                
                if ($stmt->execute()) {
                    $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
                    if ($stmtLog = $conn->prepare($queryLog)) {
                        $descripcion = 'El ticket se ha actualizado correctamente, numero de ticket: ' . $no_ticket;
                        $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario, $descripcion, $proyecto);
                        $stmtLog->execute();
                        $stmtLog->close();
                    }
                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                          <script>
                            window.onload = () => {
                                Swal.fire({
                                    title: 'Éxito',
                                    text: 'El ticket se ha actualizado correctamente.',
                                    icon: 'success',
                                    confirmButtonText: 'Aceptar'
                                }).then(() => window.location.href = '../Views/cyc.php');
                            }
                          </script>";
                } else {
                    // Si ocurre un error aquí, es la línea que genera el fatal error.
                    throw new mysqli_sql_exception($stmt->error, $stmt->errno);
                }
                $stmt->close();
            } else {
                echo "Error en la preparación de la consulta: " . $conn->error;
            }
        }
        break;

    case 5: // Eliminar ticket
        $id_cyc = $_GET['id'] ?? 0;
        if ($id_cyc > 0) {
            $query = "UPDATE cyc SET status_cyc = 0 WHERE id_cyc = ? AND proyecto = ?";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("is", $id_cyc, $proyecto);
                if ($stmt->execute()) {
                    // Log
                }
                $stmt->close();
            }
        }
        header("Location: ../Views/cyc.php");
        exit;
        break;

    case 6: // Alternar estado (activo <-> programado)
        $id_cyc = $_GET['id'] ?? 0;
        if ($id_cyc > 0) {
            $queryStatus = "SELECT status_cyc FROM cyc WHERE id_cyc = ? AND proyecto = ?";
            if ($stmtStatus = $conn->prepare($queryStatus)) {
                $stmtStatus->bind_param("is", $id_cyc, $proyecto);
                $stmtStatus->execute();
                $stmtStatus->bind_result($statusActual);
                $stmtStatus->fetch();
                $stmtStatus->close();

                if ($statusActual !== null) {
                    $nuevoStatus = ($statusActual == 1) ? 3 : 1; 
                    $accionVerbo = ($statusActual == 1) ? "programado" : "activado";

                    $queryUpdate = "UPDATE cyc SET status_cyc = ? WHERE id_cyc = ? AND proyecto = ?";
                    if ($stmtUpdate = $conn->prepare($queryUpdate)) {
                        $stmtUpdate->bind_param("iis", $nuevoStatus, $id_cyc, $proyecto);
                        if ($stmtUpdate->execute()) {
                            // Log
                        }
                        $stmtUpdate->close();
                    }
                }
            }
        }
        header("Location: ../Views/cyc.php");
        exit;
        break;

    default:
        echo "Acción no reconocida.";
}
?>