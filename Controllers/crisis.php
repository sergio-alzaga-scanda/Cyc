<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php"); // Aquí $conn debe ser instancia mysqli

$id_usuario      = $_SESSION['usuario'];
$nombre_usuario  = $_SESSION['nombre_usuario'];
$proyecto        = $_SESSION['proyecto'] ?? '';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fechaActual     = date("Y-m-d H:i:s");
$fechaHoraActual = $fechaActual;

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

switch ($accion) {

    case 1: // Crear o registrar un ticket
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $no_ticket         = $_POST['no_ticket'] ?? '';
            $nombre            = $_POST['nombre'] ?? '';
            $fecha             = $_POST['fecha_programacion'] ?? null;

            if ($fecha) {
                $status       = 2;
                $fecha_string = trim($fecha);
                try {
                    $fecha_obj          = new DateTime($fecha_string);
                    $fecha_programacion = $fecha_obj->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    echo "Error al procesar la fecha: " . $e->getMessage();
                    exit;
                }
            } else {
                $status             = 1;
                $fecha_programacion = null;
            }

            $criticidad          = $_POST['criticidad'] ?? '';
            $tipo                = $_POST['tipo'] ?? '';
            $ubicacion           = $_POST['ubicacion'] ?? '';
            $ivr_texto           = $_POST['ivr'] ?? '';
            $redaccion_canales   = $_POST['redaccion_canales'] ?? '';

            $canales             = $_POST['canal'] ?? [];
            $bots                = $_POST['bot'] ?? [];
            $mismo_canal         = isset($_POST['mismo-canal']) ? 'Sí' : 'No';
            $canal_digital_texto = $_POST['canal-digital-texto'] ?? '';

            $canales_json = json_encode($canales);
            $bots_json    = json_encode($bots);

            // Verificar si el ticket ya existe
            $query_check = "SELECT COUNT(*) FROM cyc WHERE no_ticket = ? AND status_cyc IN (1, 2) AND proyecto = ?";

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
                        nombre,
                        no_ticket,
                        categoria_cyc,
                        tipo_cyc,
                        ubicacion_cyc,
                        redaccion_cyc,
                        canal_cyc,
                        bot_cyc,
                        redaccion_canal_cyc,
                        fecha_registro_cyc,
                        status_cyc,
                        fecha_programacion,
                        id_usuario,
                        redaccion_canales,
                        proyecto
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)";

                    if ($stmt = $conn->prepare($query)) {
                        // Para bind_param, los tipos deben coincidir:
                        // s = string, i = int
                        // Asumiendo criticidad, tipo, ubicacion son enteros
                        // Ajusta según la estructura real de tu tabla

                        // Para manejar fecha_programacion que puede ser NULL,
                        // MySQLi no acepta bind_param con null directamente,
                        // así que usaremos una variable para pasar.

                        $fecha_programacion_param = $fecha_programacion ?? null;

                        // bind_param no acepta NULL, hay que usar 's' y pasar '' o pasarlo directamente
                        // Aquí pasaremos fecha_programacion como string o NULL, 
                        // y antes de la ejecución asignamos NULL para que MySQL lo interprete bien.
                        // Sin embargo, para simplificar, vamos a pasar un string o NULL con mysqli_stmt::send_long_data
                        // Pero lo más común es pasar un string vacío para NULL o el valor.

                        // Por seguridad convertimos NULL a string vacío para bind_param y luego en la consulta MySQL lo interpreta bien.
                        if ($fecha_programacion_param === null) {
                            $fecha_programacion_param = null;
                        }

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
                            $status,
                            $fecha_programacion_param,
                            $id_usuario,
                            $redaccion_canales,
                            $proyecto
                        );

                        if ($stmt->execute()) {
                            // Log
                            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) 
                                         VALUES (NOW(), ?, ?, ?, ?)";
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

    case 2: // Obtener tabla de tickets
        $DtosTbl = [];

        $queryTbl = "
            SELECT 
                c.id_cyc,
                c.no_ticket,
                c.status_cyc,
                cc.nombre_crisis AS categoria_nombre,
                CASE 
                    WHEN c.tipo_cyc = 1 THEN 'Crisis'
                    WHEN c.tipo_cyc = 2 THEN 'Contingencia'
                    ELSE 'Desconocido'
                END AS tipo_cyc,
                c.ubicacion_cyc,
                ui.nombre_ubicacion_ivr AS nombre_ubicacion,
                CASE 
                    WHEN c.fecha_programacion > c.fecha_registro_cyc THEN c.fecha_programacion
                    ELSE c.fecha_registro_cyc
                END AS fecha_activacion,
                p.nombre_proyecto
            FROM 
                cyc AS c
            JOIN 
                cat_crisis AS cc ON c.categoria_cyc = cc.id
            LEFT JOIN 
                ubicacion_ivr AS ui ON c.ubicacion_cyc = ui.id_ubicacion_ivr
            LEFT JOIN 
                cat_proyectos AS p ON c.proyecto = p.id_proyecto
            WHERE 
                c.status_cyc > 0 AND c.proyecto = ?
            ORDER BY 
                c.fecha_registro_cyc DESC;
        ";

        if ($stmt = $conn->prepare($queryTbl)) {
            $stmt->bind_param("s", $proyecto);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($rowTbl = $result->fetch_assoc()) {
                if ($rowTbl['fecha_activacion']) {
                    $fechaSinMilisegundos = substr($rowTbl['fecha_activacion'], 0, 19);
                    $date = DateTime::createFromFormat('Y-m-d H:i:s', $fechaSinMilisegundos);
                    if ($date) {
                        $rowTbl['fecha_activacion'] = $date->format('d-m-Y H:i');
                    } else {
                        $rowTbl['fecha_activacion'] = 'Fecha inválida';
                    }
                }

                $DtosTbl[] = [
                    'id_cyc'           => $rowTbl['id_cyc'],
                    'no_ticket'        => $rowTbl['no_ticket'],
                    'status_cyc'       => $rowTbl['status_cyc'],
                    'categoria_nombre' => $rowTbl['categoria_nombre'],
                    'tipo_cyc'         => $rowTbl['tipo_cyc'],
                    'nombre_ubicacion' => $rowTbl['nombre_ubicacion'],
                    'ubicacion_cyc'    => $rowTbl['ubicacion_cyc'],
                    'fecha_activacion' => $rowTbl['fecha_activacion'],
                    'nombre_proyecto'  => $rowTbl['nombre_proyecto'],
                ];
            }
            $stmt->close();

            header('Content-Type: application/json');
            echo json_encode($DtosTbl);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;

    case 3: // Obtener datos de un ticket para editar
    // Activar errores para debug (quítalo en producción)
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Obtener el ID del ticket y el proyecto
    $id_cyc = $_POST['id_cyc'] ?? $_POST['id'] ?? 0;
    $proyecto = $_SESSION['proyecto'] ?? $_POST['proyecto'] ?? null;
    $result = [];

    if (!$proyecto) {
        echo json_encode(['error' => 'Proyecto no definido']);
        break;
    }

    $query = "
        SELECT 
            c.*,
            cc.nombre_crisis,
            ui.nombre_ubicacion_ivr,
            p.nombre_proyecto
        FROM 
            cyc AS c
        LEFT JOIN 
            cat_crisis AS cc ON c.categoria_cyc = cc.id
        LEFT JOIN 
            ubicacion_ivr AS ui ON c.ubicacion_cyc = ui.id_ubicacion_ivr
        LEFT JOIN 
            cat_proyectos AS p ON c.proyecto = p.id_proyecto
        WHERE 
            c.id_cyc = ? AND c.proyecto = ?
        LIMIT 1
    ";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("is", $id_cyc, $proyecto);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            // Decodificar campos JSON si existen
            $row['canal_cyc'] = json_decode($row['canal_cyc'] ?? '[]', true);
            $row['bot_cyc'] = json_decode($row['bot_cyc'] ?? '[]', true);

            $result = $row;
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
            $id_cyc             = $_POST['id_cyc'] ?? 0;
            $no_ticket          = $_POST['no_ticket'] ?? '';
            $nombre             = $_POST['nombre'] ?? '';
            $fecha              = $_POST['fecha_programacion'] ?? null;
            
            if ($fecha) {
                $status       = 2;
                $fecha_string = trim($fecha);
                try {
                    $fecha_obj          = new DateTime($fecha_string);
                    $fecha_programacion = $fecha_obj->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    echo "Error al procesar la fecha: " . $e->getMessage();
                    exit;
                }
            } else {
                $status             = 1;
                $fecha_programacion = null;
            }

            $criticidad          = $_POST['criticidad'] ?? '';
            $tipo                = $_POST['tipo'] ?? '';
            $ubicacion           = $_POST['ubicacion'] ?? '';
            $ivr_texto           = $_POST['ivr'] ?? '';
            $redaccion_canales   = $_POST['redaccion_canales'] ?? '';

            $canales             = $_POST['canal'] ?? [];
            $bots                = $_POST['bot'] ?? [];
            $mismo_canal         = isset($_POST['mismo-canal']) ? 'Sí' : 'No';
            $canal_digital_texto = $_POST['canal-digital-texto'] ?? '';

            $canales_json = json_encode($canales);
            $bots_json    = json_encode($bots);

            // Actualizar
            $query = "
                UPDATE cyc SET
                    nombre = ?,
                    no_ticket = ?,
                    categoria_cyc = ?,
                    tipo_cyc = ?,
                    ubicacion_cyc = ?,
                    redaccion_cyc = ?,
                    canal_cyc = ?,
                    bot_cyc = ?,
                    redaccion_canal_cyc = ?,
                    status_cyc = ?,
                    fecha_programacion = ?,
                    id_usuario = ?,
                    redaccion_canales = ?
                WHERE
                    id_cyc = ? AND proyecto = ?
            ";

            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param(
                    "ssiiisssssisss",
                    $nombre,
                    $no_ticket,
                    $criticidad,
                    $tipo,
                    $ubicacion,
                    $ivr_texto,
                    $canales_json,
                    $bots_json,
                    $canal_digital_texto,
                    $status,
                    $fecha_programacion,
                    $id_usuario,
                    $redaccion_canales,
                    $id_cyc,
                    $proyecto
                );

                if ($stmt->execute()) {
                    // Log
                    $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) 
                                 VALUES (NOW(), ?, ?, ?, ?)";
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
                    echo "Error al actualizar el registro: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error en la preparación de la consulta: " . $conn->error;
            }
        }
        break;

    case 5: // Eliminar ticket (cambiar status a 0)
    $id_cyc = $_POST['id_cyc'] ?? 0;

    // CORRECCIÓN: Se agrega "AND proyecto = ?" a la consulta
    $query = "UPDATE cyc SET status_cyc = 0 WHERE id_cyc = ? AND proyecto = ?";

    if ($stmt = $conn->prepare($query)) {
        // Ahora el "bind_param" coincide con la consulta (un entero 'i' y un string 's')
        $stmt->bind_param("is", $id_cyc, $proyecto);

        if ($stmt->execute()) {
            // Log
            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) 
                         VALUES (NOW(), ?, ?, ?, ?)";
            if ($stmtLog = $conn->prepare($queryLog)) {
                $descripcion = 'El ticket se ha eliminado correctamente, id: ' . $id_cyc;
                $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario, $descripcion, $proyecto);
                $stmtLog->execute();
                $stmtLog->close();
            }

            echo json_encode(['success' => true, 'message' => 'Ticket eliminado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    break;
    case 6: // Alternar estado (1 <-> 2)
    $id_cyc = $_GET['id'] ?? 0;

    // Primero obtener el status actual
    $queryStatus = "SELECT status_cyc FROM cyc WHERE id_cyc = ?";
    if ($stmtStatus = $conn->prepare($queryStatus)) {
        $stmtStatus->bind_param("i", $id_cyc);
        $stmtStatus->execute();
        $stmtStatus->bind_result($statusActual);
        $stmtStatus->fetch();
        $stmtStatus->close();

        // Alternar el status
        $nuevoStatus = ($statusActual == 1) ? 2 : 1;
        $accion = ($nuevoStatus == 1) ? "activado" : "desactivado";

        // Actualizar el nuevo estado
        $queryUpdate = "UPDATE cyc SET status_cyc = ? WHERE id_cyc = ?";
        if ($stmtUpdate = $conn->prepare($queryUpdate)) {
            $stmtUpdate->bind_param("ii", $nuevoStatus, $id_cyc);

            if ($stmtUpdate->execute()) {
                // Log
                $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) 
                             VALUES (NOW(), ?, ?, ?, ?)";
                if ($stmtLog = $conn->prepare($queryLog)) {
                    $descripcion = 'El ticket se ha ' . $accion . ' correctamente, id: ' . $id_cyc;
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
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script>
                        window.onload = () => {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error al actualizar.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            }).then(() => window.location.href = '../Views/cyc.php');
                        }
                      </script>";
            }
            $stmtUpdate->close();
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo obtener el estado actual.']);
    }
    break;

    default:
        echo "Acción no reconocida.";
}
?>
