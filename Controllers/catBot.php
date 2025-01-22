<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php");
}
include("../Controllers/bd.php");
$id_usuario = $_SESSION['usuario'];
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
            $sql = "INSERT INTO bot (nombre_bot, status) 
                    VALUES (:nombre, :status)";
            
            // Preparar la declaración
            $stmt = $conn->prepare($sql);

            // Enlazar los parámetros de forma segura
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':status', $status);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
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
            SELECT [id_bot], [nombre_bot], [status]
            FROM [contingencias].[dbo].[bot]
            WHERE status > 0
            ORDER BY nombre_bot DESC;
            ";

            // Preparar y ejecutar la consulta usando PDO
            $stmt = $conn->prepare($queryTbl);
            $stmt->execute();

            // Obtener los resultados y prepararlos
            while ($rowTbl = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Preparar los datos para la respuesta
                $DtosTbl[] = array(
                    'id' => $rowTbl['id_bot'],
                    'nombre_bot' => $rowTbl['nombre_bot'],
                    'status' => $rowTbl['status'] // Incluimos el campo status
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
        $idBot = $_POST['edit_id_bot']; // ID del bot
        $nombreBot = $_POST['nombre']; // Nombre del bot

        try {
            // Consulta SQL para actualizar el nombre del bot
            $query = "
                UPDATE [contingencias].[dbo].[bot]
                SET 
                    [nombre_bot] = :nombre_bot
                WHERE [id_bot] = :idBot;
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nombre_bot', $nombreBot);
            $stmt->bindParam(':idBot', $idBot);

            // Ejecutar la actualización
            $stmt->execute();

            // Mostrar alerta de éxito
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El bot se editó correctamente.',
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
        $id_bot = $_GET['id'];
        try {
            // Actualiza el estado del bot a '0' (eliminado/desactivado)
            $query = "UPDATE [contingencias].[dbo].[bot] 
                      SET status = 0
                      WHERE id_bot = :id_bot";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':id_bot' => $id_bot
            ]);

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El bot se eliminó correctamente.',
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
                            text: 'No se pudo eliminar el bot.',
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
        $id_bot = $_GET['id'];
        $status_inicial = $_GET['status'];

        try {
            // Cambia el estado del bot
            if ($status_inicial === '1') {
                // Si está activo (status = 1), lo cambiamos a inactivo (status = 2 o 0)
                $nuevo_status = 2;
            } else {
                // Si está inactivo (status = 0 o 2), lo cambiamos a activo (status = 1)
                $nuevo_status = 1;
            }

            $query = "UPDATE [contingencias].[dbo].[bot] 
                      SET status = :nuevo_status
                      WHERE id_bot = :id_bot";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':nuevo_status' => $nuevo_status,
                ':id_bot' => $id_bot
            ]);

            // Mensajes de éxito dependiendo del nuevo estado
            if ($nuevo_status === 2) {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script type='text/javascript'>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Éxito',
                                text: 'Se desactivó el bot.',
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
                                text: 'Se activó el bot.',
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
                            text: 'No se pudo activar/desactivar el bot.',
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
