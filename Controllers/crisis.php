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
            
            // --- INICIO DE LA MODIFICACIÓN ---
            // 1. Obtenemos el valor de la fecha y eliminamos espacios en blanco al inicio y al final.
            $fecha_input = trim($_POST['fecha_programacion'] ?? '');

            // 2. Verificamos si la cadena no está vacía después de limpiarla.
            if (!empty($fecha_input)) {
                $status = 3; // Status de "Programado"
                try {
                    $fecha_obj          = new DateTime($fecha_input);
                    $fecha_programacion = $fecha_obj->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    echo "Error al procesar la fecha: " . $e->getMessage();
                    exit;
                }
            } else {
                // 3. Si la fecha viene vacía, asignamos NULL y el status correspondiente.
                $status = 1; // Status de "Activo"
                $fecha_programacion = null;
            }
            // --- FIN DE LA MODIFICACIÓN ---

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
                    $query = "INSERT INTO cyc (nombre, no_ticket, categoria_cyc, tipo_cyc, ubicacion_cyc, redaccion_cyc, canal_cyc, bot_cyc, redaccion_canal_cyc, fecha_registro_cyc, status_cyc, fecha_programacion, id_usuario, redaccion_canales, proyecto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)";
                    if ($stmt = $conn->prepare($query)) {
                        $stmt->bind_param("ssiiissssssiss", $nombre, $no_ticket, $criticidad, $tipo, $ubicacion, $ivr_texto, $canales_json, $bots_json, $canal_digital_texto, $status, $fecha_programacion, $id_usuario, $redaccion_canales, $proyecto);
                        if ($stmt->execute()) {
                            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
                            if ($stmtLog = $conn->prepare($queryLog)) {
                                $descripcion = 'El ticket se ha registrado correctamente, numero de ticket: ' . $no_ticket;
                                $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario, $descripcion, $proyecto);
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

    // ... (case 2 y 3 sin cambios)
    case 2: // Obtener tabla de tickets
        // ... (código existente)
        break;

    case 3: // Obtener datos de un ticket para editar
        // ... (código existente)
        break;

    case 4: // Editar ticket
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            // --- INICIO DE LA MODIFICACIÓN ---
            // 1. Obtenemos el valor de la fecha y eliminamos espacios en blanco al inicio y al final.
            $fecha_input = trim($_POST['fecha_programacion'] ?? '');

            // 2. Verificamos si la cadena no está vacía después de limpiarla.
            if (!empty($fecha_input)) {
                $status = 3; // Status de "Programado"
                try {
                    $fecha_obj = new DateTime($fecha_input);
                    $fecha_programacion = $fecha_obj->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    echo "Error al procesar la fecha: " . $e->getMessage();
                    exit;
                }
            } else {
                // 3. Si la fecha viene vacía, asignamos NULL y el status correspondiente.
                $status = 1; // Status de "Activo"
                $fecha_programacion = null;
            }
            // --- FIN DE LA MODIFICACIÓN ---

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
                // Se cambió el penúltimo tipo de 'i' a 's' para que coincida con el id_cyc y el proyecto al final
                $stmt->bind_param("ssiiissssssissi", $nombre, $no_ticket, $criticidad, $tipo, $ubicacion, $ivr_texto, $canales_json, $bots_json, $canal_digital_texto, $status, $fecha_programacion, $id_usuario, $redaccion_canales, $id_cyc, $proyecto);
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
                    echo "Error al ejecutar la actualización: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error en la preparación de la consulta: " . $conn->error;
            }
        }
        break;

    // ... (case 5 y 6 sin cambios)
    case 5: // Eliminar ticket
        // ... (código existente)
        break;

    case 6: // Alternar estado
        // ... (código existente)
        break;

    default:
        echo "Acción no reconocida.";
}
?>