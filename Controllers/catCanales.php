<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php"); // Aquí $conn es mysqli
$id_usuario           = $_SESSION['usuario'];
$nombre_usuario_login = $_SESSION['nombre_usuario'];
$proyecto             = $_SESSION['proyecto']; // Obtenemos el proyecto de la sesión

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fechaActual = date("Y-m-d H:i:s");
$fechaHoraActual = $fechaActual;

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

switch ($accion) {
    case 1:
        // Insertar canal digital con proyecto
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = $_POST['nombre'];
            $status = 1;

            $sql = "INSERT INTO canal_digital (nombre_canal, status, proyecto) VALUES (?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("Error en preparación: " . $conn->error);
            }

            $stmt->bind_param("sis", $nombre, $status, $proyecto);

            if ($stmt->execute()) {
                // Insertar log con NOW()
                $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
                $stmtLog = $conn->prepare($queryLog);
                if ($stmtLog === false) {
                    die("Error en preparación log: " . $conn->error);
                }

                $descripcion = 'Ha creado un Canal Digital de nombre: ' . $nombre;
                $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
                $stmtLog->execute();

                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script type='text/javascript'>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Éxito',
                                text: 'Se guardó el registro correctamente.',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            }).then(function() {
                                window.location.href = '../Views/catalogos.php';
                            });
                        }
                      </script>";
            } else {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script type='text/javascript'>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'Ocurrió un error al guardar el registro.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            }).then(function() {
                                window.location.href = '../Views/catalogos.php';
                            });
                        }
                      </script>";
            }
            $stmt->close();
        }
        $conn->close();
        break;

    case 2:
        $DtosTbl = array();
        $sql = "SELECT id_canal_digital, nombre_canal, status, fecha_registro, fecha_actualizacion
                FROM canal_digital
                WHERE status > 0 AND proyecto = ?
                ORDER BY nombre_canal DESC";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(['error' => $conn->error]);
            exit;
        }
        $stmt->bind_param("s", $proyecto);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($rowTbl = $result->fetch_assoc()) {
            $DtosTbl[] = array(
                'id'                  => $rowTbl['id_canal_digital'],
                'nombre_canal'        => $rowTbl['nombre_canal'],
                'status'              => $rowTbl['status'],
                'fecha_registro'      => $rowTbl['fecha_registro'],
                'fecha_actualizacion' => $rowTbl['fecha_actualizacion']
            );
        }

        header('Content-Type: application/json');
        echo json_encode($DtosTbl);

        $stmt->close();
        $conn->close();
        break;

    case 3:
        $idCanal     = $_POST['edit_id_canal'];
        $nombreCanal = $_POST['nombre'];

        $sql = "UPDATE canal_digital SET nombre_canal = ? WHERE id_canal_digital = ? AND proyecto = ?";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error en preparación: " . $conn->error);
        }

        $stmt->bind_param("sis", $nombreCanal, $idCanal, $proyecto);

        if ($stmt->execute()) {
            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
            $stmtLog = $conn->prepare($queryLog);
            if ($stmtLog === false) {
                die("Error en preparación log: " . $conn->error);
            }

            $descripcion = 'Ha editado un Canal Digital de nombre: ' . $nombreCanal . ' ID: ' . $idCanal;
            $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
            $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El canal se editó correctamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al editar el registro.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        }
        $stmt->close();
        $conn->close();
        break;

    case 4:
        $id_canal = $_GET['id'];

        $sql = "UPDATE canal_digital SET status = 0 WHERE id_canal_digital = ? AND proyecto = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error en preparación: " . $conn->error);
        }

        $stmt->bind_param("is", $id_canal, $proyecto);

        if ($stmt->execute()) {
            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
            $stmtLog = $conn->prepare($queryLog);
            if ($stmtLog === false) {
                die("Error en preparación log: " . $conn->error);
            }

            $descripcion = 'Ha eliminado un Canal Digital con ID: ' . $id_canal;
            $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
            $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El canal se eliminó correctamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo eliminar el canal.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        }
        $stmt->close();
        $conn->close();
        break;

    case 5:
        $id_canal       = $_GET['id'];
        $status_inicial = $_GET['status'];

        $nuevo_status = ($status_inicial === '1') ? 2 : 1;

        $sql = "UPDATE canal_digital SET status = ? WHERE id_canal_digital = ? AND proyecto = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error en preparación: " . $conn->error);
        }

        $stmt->bind_param("iis", $nuevo_status, $id_canal, $proyecto);

        if ($stmt->execute()) {
            $msg = ($nuevo_status === 2) ? 'Se desactivó el canal.' : 'Se activó el canal.';

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: '$msg',
                            icon: 'info',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/catalogos.php';
                        });
                    }
                  </script>";
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo activar/desactivar el canal.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
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
?>
