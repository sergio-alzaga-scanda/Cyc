<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
    exit;
}

include("../Controllers/bd.php");

$id_usuario           = $_SESSION['usuario'];
$nombre_usuario_login = $_SESSION['nombre_usuario'];
$proyecto             = $_SESSION['proyecto'];  // NUEVO: Proyecto de sesión

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fechaActual = date("Y-m-d H:i:s");
$fechaHoraActual = $fechaActual;

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

switch ($accion) {
    case 1:
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre     = $_POST['nombre'];
            $criticidad = $_POST['criticidad'];
            $status     = 1; // Activo

            $sql = "INSERT INTO cat_crisis (nombre_crisis, criticidad, status, proyecto) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param("ssis", $nombre, $criticidad, $status, $proyecto);

            if ($stmt->execute()) {
                // Insertar log
                $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (?, ?, ?, ?, ?)";
                $stmtLog = $conn->prepare($queryLog);
                if ($stmtLog === false) {
                    die("Error en preparación logs: " . $conn->error);
                }
                $descripcion = 'Ha creado una CyCs de nombre: ' . $nombre . ' y Criticidad: ' . $criticidad;
                $fecha_log = date("Y-m-d H:i:s");
                $stmtLog->bind_param("sisss", $fecha_log, $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
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
                              text: 'Ocurrió un error al guardar el registro',
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
        }
        break;

    case 2:
        $DtosTbl = array();

        $queryTbl = "SELECT id, nombre_crisis, criticidad, status FROM cat_crisis WHERE status > 0 AND proyecto = ? ORDER BY nombre_crisis DESC";
        $stmt = $conn->prepare($queryTbl);
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("s", $proyecto);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($rowTbl = $result->fetch_assoc()) {
            $DtosTbl[] = array(
                'id'            => $rowTbl['id'],
                'nombre_crisis' => $rowTbl['nombre_crisis'],
                'criticidad'    => $rowTbl['criticidad'],
                'status'        => $rowTbl['status']
            );
        }

        header('Content-Type: application/json');
        echo json_encode($DtosTbl);
        $stmt->close();
        break;

    case 3:
        $idCrisis     = $_POST['id']; 
        $nombreCrisis = $_POST['nombre']; 
        $criticidad   = (int)$_POST['criticidad']; 
        $status       = (int)$_POST['status']; 

        $query = "UPDATE cat_crisis SET nombre_crisis = ?, criticidad = ?, status = ?, fecha_modificacion = ?, proyecto = ? WHERE id = ? AND proyecto = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $fecha_modificacion = date("Y-m-d H:i:s");
        $stmt->bind_param("siissis", $nombreCrisis, $criticidad, $status, $fecha_modificacion, $proyecto, $idCrisis, $proyecto);

        if ($stmt->execute()) {
            // Log de edición
            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (?, ?, ?, ?, ?)";
            $stmtLog = $conn->prepare($queryLog);
            if ($stmtLog === false) {
                die("Error en preparación logs: " . $conn->error);
            }
            $descripcion = 'Ha editado una CyCs de nombre: ' . $nombreCrisis . ' y Criticidad: ' . $criticidad . ' ID: ' . $idCrisis;
            $fecha_log = date("Y-m-d H:i:s");
            $stmtLog->bind_param("sisss", $fecha_log, $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
            $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                  window.onload = function() {
                      Swal.fire({
                          title: 'Éxito',
                          text: 'La categoría de crisis se editó correctamente.',
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
        break;

    case 4:
        $id_cyc = $_GET['id'];

        $query = "UPDATE cat_crisis SET status = 0 WHERE id = ? AND proyecto = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("is", $id_cyc, $proyecto);

        if ($stmt->execute()) {
            // Log eliminación
            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (?, ?, ?, ?, ?)";
            $stmtLog = $conn->prepare($queryLog);
            if ($stmtLog === false) {
                die("Error en preparación logs: " . $conn->error);
            }
            $descripcion = 'Ha eliminado una CyCs con ID: ' . $id_cyc;
            $fecha_log = date("Y-m-d H:i:s");
            $stmtLog->bind_param("sisss", $fecha_log, $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
            $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                  window.onload = function() {
                      Swal.fire({
                          title: 'Éxito',
                          text: 'La crisis se eliminó correctamente.',
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
                          text: 'No se pudo eliminar la crisis',
                          icon: 'error',
                          confirmButtonText: 'Aceptar'
                      }).then(function() {
                          window.location.href = '../Views/catalogos.php';
                      });
                  }
                  </script>";
        }
        $stmt->close();
        break;

    case 5:
        $id_cyc         = $_GET['id'];
        $status_inicial = $_GET['status'];

        $nuevo_status = ($status_inicial === '1') ? 2 : 1;

        $query = "UPDATE cat_crisis SET status = ? WHERE id = ? AND proyecto = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("iis", $nuevo_status, $id_cyc, $proyecto);

        if ($stmt->execute()) {
            $textoMensaje = ($nuevo_status === 2) ? 'Se desactivó la crisis.' : 'Se activó la crisis.';

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                  window.onload = function() {
                      Swal.fire({
                          title: 'Éxito',
                          text: '$textoMensaje',
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
                          text: 'No se pudo activar/desactivar la crisis.',
                          icon: 'error',
                          confirmButtonText: 'Aceptar'
                      }).then(function() {
                          window.location.href = '../Views/catalogos.php';
                      });
                  }
                  </script>";
        }
        $stmt->close();
        break;

    default:
        echo "Acción no reconocida.";
}
?>
