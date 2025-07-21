<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php"); // bd.php debe usar mysqli

$id_usuario           = $_SESSION['usuario'];
$nombre_usuario_login = $_SESSION['nombre_usuario'];
$proyecto             = $_SESSION['proyecto']; // Nuevo dato proyecto

// Mostrar errores para desarrollo (quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

switch ($accion) {
    case 1:
        // Insertar nuevo bot con proyecto
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = $_POST['nombre'] ?? null;
            $status = 1;

            if (!$nombre) {
                echo "El nombre del bot es obligatorio.";
                exit;
            }

            $sql = "INSERT INTO bot (nombre_bot, status, proyecto) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Error en prepare: " . $conn->error);
            }
            $stmt->bind_param("sis", $nombre, $status, $proyecto);

            if ($stmt->execute()) {
                // Log
                $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
                $stmtLog = $conn->prepare($queryLog);
                if (!$stmtLog) {
                    die("Error en prepare log: " . $conn->error);
                }
                $descripcion = 'Ha creado un Bot de nombre: ' . $nombre;
                $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
                $stmtLog->execute();

                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Éxito',
                                text: 'Se guardó el registro correctamente.',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                window.location.href = '../Views/catalogos.php';
                            });
                        }
                      </script>";
            } else {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'Ocurrió un error al guardar el registro.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                window.location.href = '../Views/catalogos.php';
                            });
                        }
                      </script>";
            }

            $stmt->close();
            if (isset($stmtLog)) $stmtLog->close();
            $conn->close();
        }
        break;

    case 2:
        $DtosTbl = [];
        $queryTbl = "SELECT id_bot, nombre_bot, status FROM bot WHERE status > 0 AND proyecto = ? ORDER BY nombre_bot DESC";

        $stmt = $conn->prepare($queryTbl);
        if (!$stmt) {
            echo json_encode(['error' => $conn->error]);
            exit;
        }
        $stmt->bind_param("s", $proyecto);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($rowTbl = $result->fetch_assoc()) {
            $DtosTbl[] = [
                'id' => $rowTbl['id_bot'],
                'nombre_bot' => $rowTbl['nombre_bot'],
                'status' => $rowTbl['status'],
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($DtosTbl);

        $stmt->close();
        $conn->close();
        break;

    case 3:
        $idBot     = $_POST['edit_id_bot'] ?? null;
        $nombreBot = $_POST['nombre'] ?? null;

        if (!$idBot || !$nombreBot) {
            echo "Datos incompletos para actualizar.";
            exit;
        }

        $query = "UPDATE bot SET nombre_bot = ? WHERE id_bot = ? AND proyecto = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error en prepare: " . $conn->error);
        }
        $stmt->bind_param("sis", $nombreBot, $idBot, $proyecto);

        if ($stmt->execute()) {
            // Log
            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
            $stmtLog = $conn->prepare($queryLog);
            if (!$stmtLog) {
                die("Error en prepare log: " . $conn->error);
            }
            $descripcion = "Ha editado un Bot de nombre: $nombreBot ID: $idBot";
            $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
            $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El bot se editó correctamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al editar el registro.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        }

        $stmt->close();
        if (isset($stmtLog)) $stmtLog->close();
        $conn->close();
        break;

    case 4:
        $id_bot = $_GET['id'] ?? null;

        if (!$id_bot) {
            echo "ID de bot no proporcionado.";
            exit;
        }

        $query = "UPDATE bot SET status = 0 WHERE id_bot = ? AND proyecto = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error en prepare: " . $conn->error);
        }
        $stmt->bind_param("is", $id_bot, $proyecto);

        if ($stmt->execute()) {
            // Log
            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
            $stmtLog = $conn->prepare($queryLog);
            if (!$stmtLog) {
                die("Error en prepare log: " . $conn->error);
            }
            $descripcion = 'Ha eliminado un Bot con ID: ' . $id_bot;
            $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
            $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El bot se eliminó correctamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo eliminar el bot.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        }

        $stmt->close();
        if (isset($stmtLog)) $stmtLog->close();
        $conn->close();
        break;

    case 5:
        $id_bot         = $_GET['id'] ?? null;
        $status_inicial = $_GET['status'] ?? null;

        if (!$id_bot || !in_array($status_inicial, ['1', '2'])) {
            echo "Datos inválidos para cambiar estado.";
            exit;
        }

        $nuevo_status = ($status_inicial === '1') ? 2 : 1;

        $query = "UPDATE bot SET status = ? WHERE id_bot = ? AND proyecto = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error en prepare: " . $conn->error);
        }
        $stmt->bind_param("iis", $nuevo_status, $id_bot, $proyecto);

        if ($stmt->execute()) {
            $mensaje = ($nuevo_status === 2) ? 'Se desactivó el bot.' : 'Se activó el bot.';

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: '$mensaje',
                            icon: 'info',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo activar/desactivar el bot.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        }

        $stmt->close();
        $conn->close();
        break;

    default:
        echo "Acción no reconocida.";
}
