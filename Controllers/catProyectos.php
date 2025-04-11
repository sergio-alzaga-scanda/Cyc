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
            $status = 1;

            // Preparar la consulta de inserción
            $sql = "INSERT INTO cat_proyectos (nombre_proyecto, status) 
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
                // $descripcion = 'Ha creado un Proyecto de nombre: ' . $nombre ;
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
            SELECT [id_proyecto], [nombre_proyecto], [status]
            FROM [contingencias].[dbo].[cat_proyectos]
            WHERE status > 0
            ORDER BY nombre_proyecto DESC;
            ";

            // Preparar y ejecutar la consulta usando PDO
            $stmt = $conn->prepare($queryTbl);
            $stmt->execute();

            // Obtener los resultados y prepararlos
            while ($rowTbl = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Preparar los datos para la respuesta
                $DtosTbl[] = array(
                    'id'              => $rowTbl['id_proyecto'],
                    'nombre_proyecto' => $rowTbl['nombre_proyecto'],
                    'status'          => $rowTbl['status'] // Incluimos el campo status
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
        $idProyecto     = $_POST['edit_id_proyecto']; // ID del proyecto
        $nombreProyecto = $_POST['nombre']; // Nombre del proyecto

        try {
            // Consulta SQL para actualizar el nombre del proyecto
            $query = "
                UPDATE [contingencias].[dbo].[cat_proyectos]
                SET 
                    [nombre_proyecto] = :nombre_proyecto
                WHERE [id_proyecto] = :idProyecto;
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nombre_proyecto', $nombreProyecto);
            $stmt->bindParam(':idProyecto', $idProyecto);

            // Ejecutar la actualización
            $stmt->execute();

            // // Insertar en la tabla logs 
            // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
            //              VALUES (GETDATE(), :user_id, :name_user, :description)";
            // $stmtLog = $conn->prepare($queryLog);
            // $stmtLog->bindParam(':user_id', $id_usuario);
            // $stmtLog->bindParam(':name_user', $nombre_usuario_login);
            // $descripcion = 'Ha editado un Proyecto de nombre: ' . $nombreProyecto . ' ID: ' . $idProyecto ;
            // $stmtLog->bindParam(':description', $descripcion);
            // $stmtLog->execute();

            // Mostrar alerta de éxito
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El proyecto se editó correctamente.',
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
        $id_proyecto = $_GET['id'];
        try {
            // Actualiza el estado del proyecto a '0' (eliminado/desactivado)
            $query = "UPDATE [contingencias].[dbo].[cat_proyectos] 
                      SET status = 0
                      WHERE id_proyecto = :id_proyecto";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':id_proyecto' => $id_proyecto
            ]);

            // // Insertar en la tabla logs 
            // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
            //              VALUES (GETDATE(), :user_id, :name_user, :description)";
            // $stmtLog = $conn->prepare($queryLog);
            // $stmtLog->bindParam(':user_id', $id_usuario);
            // $stmtLog->bindParam(':name_user', $nombre_usuario_login);
            // $descripcion = 'Ha eliminado un Proyecto con ID: ' . $id_proyecto ;
            // $stmtLog->bindParam(':description', $descripcion);
            // $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El proyecto se eliminó correctamente.',
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
                            text: 'No se pudo eliminar el proyecto.',
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
        $id_proyecto    = $_GET['id'];
        $status_inicial = $_GET['status'];

        try {
            // Cambia el estado del proyecto
            if ($status_inicial === '1') {
                // Si está activo (status = 1), lo cambiamos a inactivo (status = 2 o 0)
                $nuevo_status = 2;
            } else {
                // Si está inactivo (status = 0 o 2), lo cambiamos a activo (status = 1)
                $nuevo_status = 1;
            }

            $query = "UPDATE [contingencias].[dbo].[cat_proyectos] 
                      SET status = :nuevo_status
                      WHERE id_proyecto = :id_proyecto";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':nuevo_status' => $nuevo_status,
                ':id_proyecto' => $id_proyecto
            ]);

            // Mensajes de éxito dependiendo del nuevo estado
            if ($nuevo_status === 2) {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script type='text/javascript'>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Éxito',
                                text: 'Se desactivó el proyecto.',
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
                                text: 'Se activó el proyecto.',
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
                            text: 'No se pudo activar/desactivar el proyecto.',
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
