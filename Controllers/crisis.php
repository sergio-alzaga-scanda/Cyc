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
        $proyecto_nuevo = $_POST['proyecto'] ?? '';
        $canales_json        = json_encode($canales);
        $bots_json           = json_encode($bots);

        // 🔹 Limpiar saltos de línea de redaccion_canales
        $redaccion_canales = preg_replace("/[\r\n]+/", " ", trim($redaccion_canales));

        $query_check = "SELECT COUNT(*) FROM cyc WHERE no_ticket = ? AND status_cyc IN (1, 3) AND proyecto = ?";

        if ($stmt_check = $conn->prepare($query_check)) {
            $stmt_check->bind_param("ss", $no_ticket, $proyecto_nuevo);
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
                        "ssiiissssssisss",  // ← ahora con 15 letras
                        $nombre,
                        $no_ticket,
                        $criticidad,
                        $tipo,
                        $ubicacion,
                        $ivr_texto,
                        $canales_json,
                        $bots_json,
                        $canal_digital_texto,
                        $fecha_registro,         
                        $status,
                        $fecha_programacion,
                        $id_usuario,
                        $redaccion_canales,
                        $proyecto_nuevo
                    );

                    if ($stmt->execute()) {
                        $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto)
                                     VALUES (?, ?, ?, ?, ?)";

                        if ($stmtLog = $conn->prepare($queryLog)) {
                            $descripcion = 'El ticket se ha registrado correctamente, número de ticket: ' . $no_ticket;
                            $fecha_log   = date('Y-m-d H:i:s');
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
                        echo 'Error al insertar el registro: ' . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo 'Error en la preparación de la consulta: ' . $conn->error;
                }
            }
        } else {
            echo 'Error en la preparación de la consulta: ' . $conn->error;
        }
    }
    break;


    case 2: // Obtener tabla de tickets
    $DtosTbl = [];

    // Condición para filtrar tickets según perfil
    $filtroUsuario = '';
    $parametros = [];
    $tipos = '';

    if ($_SESSION['perfil'] != 1) { // Si NO es administrador
        $filtroUsuario = ' AND c.id_usuario = ? ';
        $parametros[] = $_SESSION['usuario'];
        $tipos .= 'i';
    }

    $queryTbl = "
        SELECT 
            c.id_cyc, c.no_ticket, c.status_cyc, cc.nombre_crisis AS categoria_nombre,
            CASE
            WHEN c.tipo_cyc = 1 THEN 'Crisis'
            WHEN c.tipo_cyc = 2 THEN 'Contingencia' 
            WHEN c.tipo_cyc = 3 THEN 'Dia asueto'
            ELSE 'Desconocido' END AS tipo_cyc,
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
        WHERE c.status_cyc > 0 $filtroUsuario
        ORDER BY c.fecha_registro_cyc DESC
    ";

    if ($stmt = $conn->prepare($queryTbl)) {
        // Bind solo si hay filtro
        if (!empty($parametros)) {
            $stmt->bind_param($tipos, ...$parametros);
        }

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
        $query = "SELECT c.*, cc.nombre_crisis, ui.nombre_ubicacion_ivr, p.nombre_proyecto FROM cyc AS c LEFT JOIN cat_crisis AS cc ON c.categoria_cyc = cc.id LEFT JOIN ubicacion_ivr AS ui ON c.ubicacion_cyc = ui.id_ubicacion_ivr LEFT JOIN cat_proyectos AS p ON c.proyecto = p.id_proyecto WHERE c.id_cyc = ?  LIMIT 1";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $id_cyc);
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

        /* --------------------- FECHA --------------------- */
        $fecha = $_POST['fecha_programacion_2'] ?? null;

        if ($fecha) {
            $status = 3;
            try {
                $fecha_obj = new DateTime(trim($fecha));
                $fecha_programacion = $fecha_obj->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                echo "Error al procesar la fecha: " . $e->getMessage();
                exit;
            }
        } else {
            $status = 1;
            $fecha_programacion = null;
        }


        /* --------------------- CAMPOS POST --------------------- */
        $id_cyc            = $_POST['id'] ?? 0;
        $no_ticket         = $_POST['no_ticket'] ?? '';
        $nombre            = $_POST['nombre'] ?? '';
        $criticidad        = $_POST['categoria_edit'] ?? '';
        $tipo              = $_POST['tipo_edit'] ?? '';
        $ubicacion         = $_POST['ubicacion_edit'] ?? '';
        $ivr_texto         = $_POST['ivr_edit'] ?? '';
        $proyecto          = $_POST['edit_proyecto'] ?? 0;

        // Canal digital
        $redaccion_canales = $_POST['redaccion_canales_edit'] ?? '';
        $canales           = $_POST['canal_edit'] ?? [];
        $bots              = $_POST['bot_edit'] ?? [];
        $canal_digital_txt = $_POST['redaccion_canales_edit'] ?? '';

        $canales_json = json_encode($canales);
        $bots_json    = json_encode($bots);

        // Limpieza
        $redaccion_canales = preg_replace("/[\r\n]+/", " ", trim($redaccion_canales));


        /* --------------------- QUERY --------------------- */
        $query = "UPDATE cyc 
                    SET nombre = ?, 
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
                        redaccion_canales = ?, 
                        proyecto = ?
                  WHERE id_cyc = ?";


        if ($stmt = $conn->prepare($query)) {

            $stmt->bind_param(
                "ssiiissssssssii",
                $nombre,
                $no_ticket,
                $criticidad,
                $tipo,
                $ubicacion,
                $ivr_texto,
                $canales_json,
                $bots_json,
                $canal_digital_txt,
                $status,
                $fecha_programacion,
                $id_usuario,
                $redaccion_canales,
                $proyecto,   // ← ahora se actualiza correctamente
                $id_cyc
            );

            if ($stmt->execute()) {

                /* -------- REGISTRO DE LOG -------- */
                $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) 
                             VALUES (NOW(), ?, ?, ?, ?)";

                if ($stmtLog = $conn->prepare($queryLog)) {
                    $descripcion = 'El ticket se ha actualizado correctamente, número de ticket: ' . $no_ticket;
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
                throw new mysqli_sql_exception($stmt->error, $stmt->errno);
            }

            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        }
    }
    break;


    case 5:

    $id_cyc = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id_cyc <= 0) {
        header("Location: ../Views/cyc.php?status=err"); 
        exit;
    }

    $query = "UPDATE cyc SET status_cyc = 0 WHERE id_cyc = ?";

    if ($stmt = $conn->prepare($query)) {

        $stmt->bind_param("i", $id_cyc);

        if (!$stmt->execute()) {
            header("Location: ../Views/cyc.php?status=err");
            exit;
        }

        $stmt->close();

    } else {
        header("Location: ../Views/cyc.php?status=err");
        exit;
    }

    // Pequeño delay opcional
    sleep(1);

    header("Location: ../Views/cyc.php?status=ok");
    exit;

break;

    case 6:
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Obtenemos el status actual
        $sqlSelect = "SELECT status_cyc FROM cyc WHERE id_cyc = ?";
        if ($stmt = $conn->prepare($sqlSelect)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($statusActual);
            if ($stmt->fetch()) {
                $stmt->close();

                // Invertimos entre 1 y 2
                if ($statusActual == 1) {
                    $nuevoStatus = 2;
                } elseif ($statusActual == 2) {
                    $nuevoStatus = 1;
                } else {
                    $nuevoStatus = $statusActual; // otros valores no cambian
                }

                // Actualizamos en la DB
                $sqlUpdate = "UPDATE cyc SET status_cyc = ? WHERE id_cyc = ?";
                if ($stmtUpdate = $conn->prepare($sqlUpdate)) {
                    $stmtUpdate->bind_param("ii", $nuevoStatus, $id);
                    if ($stmtUpdate->execute()) {
                        $stmtUpdate->close();
                        header("Location: ../Views/cyc.php?msg=Estado actualizado");
                        exit;
                    } else {
                        echo "Error al actualizar el estado: " . $stmtUpdate->error;
                    }
                }
            } else {
                echo "Registro no encontrado";
            }
        } else {
            echo "Error en la consulta: " . $conn->error;
        }
    } else {
        echo "ID no especificado";
    }
    break;


    default:
        echo "Acción no reconocida.";
}
?>