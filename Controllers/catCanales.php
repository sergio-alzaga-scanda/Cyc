<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php");
}
include("../Controllers/bd.php");
$id_usuario           = $_SESSION['usuario'];
$nombre_usuario_login = $_SESSION['nombre_usuario'];
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fechaActual = date("Y-m-d H:i:s");
$fechaHoraActual = $fechaActual;

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

switch ($accion) {
    case 1:
        // Verificar que se ha enviado el formulario
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Capturar los datos del formulario
            $nombre = $_POST['nombre'];
            $status = 1; // El status que deseas asignar, por ejemplo 1 para "activo"

            // Preparar la consulta de inserción
            $sql = "INSERT INTO canal_digital (nombre_canal, status) 
                    VALUES (:nombre, :status)";
            
            // Preparar la declaración
            $stmt = $conn->prepare($sql);

            // Enlazar los parámetros de forma segura
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':status', $status);
            

            // Ejecutar la consulta
            if ($stmt->execute()) {

                // // Insertar en la tabla logs 
                // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                //              VALUES (GETDATE(), :user_id, :name_user, :description)";
                // $stmtLog = $conn->prepare($queryLog);
                // $stmtLog->bindParam(':user_id', $id_usuario);
                // $stmtLog->bindParam(':name_user', $nombre_usuario_login);
                // $descripcion = 'Ha creado un Canal Digital de nombre: ' . $nombre ;
                // $stmtLog->bindParam(':description', $descripcion);
                // $stmtLog->execute();

                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script type='text/javascript'>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Éxito',
                                text: 'Se guardó el registro correctamente.',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            }).then(function() {
                                window.location.href = '../Views/catalogos.php'; // Redirige a la página de éxito
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
                                window.location.href = '../Views/catalogos.php'; // Redirige a la página de error
                            });
                        }
                      </script>";
            }
        }

        // Cerrar la conexión
        $conn = null;
        break;

    case 2:
        $DtosTbl = array();

        try {
            // Definir la nueva consulta
            $queryTbl = "
            SELECT [id_canal_digital], [nombre_canal], [status], [fecha_registro], [fecha_actualizacion]
            FROM [contingencias].[dbo].[canal_digital]
            WHERE status > 0
            ORDER BY nombre_canal DESC;
            ";

            // Preparar y ejecutar la consulta usando PDO
            $stmt = $conn->prepare($queryTbl);
            $stmt->execute();

            // Obtener los resultados y prepararlos
            while ($rowTbl = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Preparar los datos para la respuesta
                $DtosTbl[] = array(
                    'id'                  => $rowTbl['id_canal_digital'],
                    'nombre_canal'        => $rowTbl['nombre_canal'],
                    'status'              => $rowTbl['status'], // Incluimos el campo status
                    'fecha_registro'      => $rowTbl['fecha_registro'],
                    'fecha_actualizacion' => $rowTbl['fecha_actualizacion']
                );
            }

            // Enviar la respuesta como JSON
            header('Content-Type: application/json');
            echo json_encode($DtosTbl);

        } catch (PDOException $e) {
            // Capturar errores de base de datos y devolverlo como JSON
            echo json_encode(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            // Capturar cualquier otro error y devolverlo como JSON
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 3:
        // Obtener los datos del formulario
        $idCanal     = $_POST['edit_id_canal']; // ID del canal
        $nombreCanal = $_POST['nombre']; // Nombre del canal

        try {
            // Consulta SQL para actualizar la categoría de canal
            $query = "
                UPDATE [contingencias].[dbo].[canal_digital]
                SET 
                    [nombre_canal] = :nombre_canal
                WHERE [id_canal_digital] = :idCanal;
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nombre_canal', $nombreCanal);
            $stmt->bindParam(':idCanal', $idCanal);

            // Ejecutar la actualización
            $stmt->execute();

            // // Insertar en la tabla logs 
            // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
            //              VALUES (GETDATE(), :user_id, :name_user, :description)";
            // $stmtLog = $conn->prepare($queryLog);
            // $stmtLog->bindParam(':user_id', $id_usuario);
            // $stmtLog->bindParam(':name_user', $nombre_usuario_login);
            // $descripcion = 'Ha editado un Canal Digital de nombre: ' . $nombreCanal . ' ID: ' . $idCanal ;
            // $stmtLog->bindParam(':description', $descripcion);
            // $stmtLog->execute();

            // Mostrar alerta de éxito
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El canal se editó correctamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/catalogos.php'; // Redirige a la página de éxito
                        });
                    }
                  </script>";
        } catch (PDOException $e) {
            // Manejo de error
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al editar el registro.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/catalogos.php'; // Redirige a la página de error
                        });
                    }
                  </script>";
        }
        break;

    case 4:
        $id_canal = $_GET['id'];
        try {
            // Actualiza el estado del canal a '0' (eliminado/desactivado)
            $query = "UPDATE [contingencias].[dbo].[canal_digital] 
                      SET status = 0
                      WHERE id_canal_digital = :id_canal";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':id_canal' => $id_canal
            ]);

            // // Insertar en la tabla logs 
            // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
            //              VALUES (GETDATE(), :user_id, :name_user, :description)";
            // $stmtLog = $conn->prepare($queryLog);
            // $stmtLog->bindParam(':user_id', $id_usuario);
            // $stmtLog->bindParam(':name_user', $nombre_usuario_login);
            // $descripcion = 'Ha eliminado un Canal Digital con ID: ' . $id_canal ;
            // $stmtLog->bindParam(':description', $descripcion);
            // $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El canal se eliminó correctamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/catalogos.php'; // Redirige a la página de éxito
                        });
                    }
                  </script>";
        } catch (PDOException $e) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo eliminar el canal.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/catalogos.php'; // Redirige a la página de error
                        });
                    }
                  </script>";
        }
        break;

    case 5:
        $id_canal       = $_GET['id'];
        $status_inicial = $_GET['status'];

        try {
            // Cambia el estado del canal
            if ($status_inicial === '1') {
                // Si está activo (status = 1), lo cambiamos a inactivo (status = 2 o 0)
                $nuevo_status = 2;
            } else {
                // Si está inactivo (status = 0 o 2), lo cambiamos a activo (status = 1)
                $nuevo_status = 1;
            }

            $query = "UPDATE [contingencias].[dbo].[canal_digital] 
                      SET status = :nuevo_status
                      WHERE id_canal_digital = :id_canal";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':nuevo_status' => $nuevo_status,
                ':id_canal'     => $id_canal
            ]);

            // Mensajes de éxito dependiendo del nuevo estado
            if ($nuevo_status === 2) {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script type='text/javascript'>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Éxito',
                                text: 'Se desactivó el canal.',
                                icon: 'info',
                                confirmButtonText: 'Aceptar'
                            }).then(function() {
                                window.location.href = '../Views/catalogos.php'; // Redirige a la página de éxito
                            });
                        }
                      </script>";
            } else {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script type='text/javascript'>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Éxito',
                                text: 'Se activó el canal.',
                                icon: 'info',
                                confirmButtonText: 'Aceptar'
                            }).then(function() {
                                window.location.href = '../Views/catalogos.php'; // Redirige a la página de éxito
                            });
                        }
                      </script>";
            }

        } catch (PDOException $e) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo activar/desactivar el canal.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/catalogos.php'; // Redirige a la página de error
                        });
                    }
                  </script>";
        }
        break;

    default:
        echo "Acción no reconocida.";
}
?>