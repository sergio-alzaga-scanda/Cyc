<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php");
    exit;
}
include("../Controllers/bd.php"); // Aquí debe estar la conexión $conn con mysqli

$id_usuario           = $_SESSION['usuario'];
$nombre_usuario_login = $_SESSION['nombre_usuario'];
$proyecto             = $_SESSION['proyecto'];  // NUEVO

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fechaActual     = date("Y-m-d H:i:s");
$fechaHoraActual = $fechaActual;

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

switch ($accion) {
     
    case 1:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre_usuario   = $_POST['nombre_usuario'];
        $correo_usuario   = $_POST['correo_usuario'];
        $pass             = 'contra12345'; // contraseña por defecto
        $puesto_usuario   = $_POST['puesto_usuario'];
        $telefono_usuario = $_POST['telefono_usuario'];
        $perfil_usuario   = $_POST['perfil_usuario'];
        $status           = $_POST['status'];
        $fecha_creacion   = $_POST['fecha_creacion'];

        // Asignar proyecto solo si existe y no es administrador
        $proyecto = null;
        if (isset($_POST['proyecto']) && !empty($_POST['proyecto'])) {
            $proyecto = $_POST['proyecto'];
        }

        $query = "
        INSERT INTO usuarios (
            nombre_usuario, 
            correo_usuario, 
            pass, 
            puesto_usuario, 
            telefono_usuario, 
            perfil_usuario, 
            status,
            proyecto,
            fecha_creacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssssssiss",
            $nombre_usuario,
            $correo_usuario,
            $pass,
            $puesto_usuario,
            $telefono_usuario,
            $perfil_usuario,
            $status,
            $proyecto,
            $fecha_creacion
        );

        if ($stmt->execute()) {
            $id_insertado = $stmt->insert_id;

            $descripcion = 'Ha registrado al usuario: ' . $nombre_usuario . ' con correo: ' . $correo_usuario;

            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
            $stmtLog = $conn->prepare($queryLog);
            if ($stmtLog === false) {
                die("Error en la preparación del log: " . $conn->error);
            }
            $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
            $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El usuario se ha registrado correctamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/usuarios.php';
                        });
                    }
                  </script>";
        } else {
            echo "Error al crear el usuario: " . $stmt->error;
        }

        $stmt->close();
    }
    break;

    case 2:
    $DtosTbl = array();

    try {
        $queryTbl = "
        SELECT 
            u.idUsuarios,
            u.nombre_usuario,
            u.correo_usuario,
            u.pass,
            u.perfil_usuario,
            u.puesto_usuario,
            u.telefono_usuario,
            u.status,
            u.fecha_creacion,
            p.nombre_perfil
        FROM 
            usuarios AS u
        JOIN 
            perfil AS p ON u.perfil_usuario = p.id_Perfil
        Where u.status > 0
        ORDER BY 
            u.fecha_creacion DESC;
        ";

        $stmt = $conn->prepare($queryTbl);
        if ($stmt === false) {
            die("Error en la preparación: " . $conn->error);
        }

        // No hay parámetros que enlazar porque ya no hay WHERE
        $stmt->execute();

        $result = $stmt->get_result();

        while ($rowTbl = $result->fetch_assoc()) {
            if ($rowTbl['fecha_creacion']) {
                $fechaSinMilisegundos = substr($rowTbl['fecha_creacion'], 0, 19);
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $fechaSinMilisegundos);
                $rowTbl['fecha_creacion'] = $date ? $date->format('d-m-Y H:i') : 'Fecha inválida';
            }

            $DtosTbl[] = array(
                'idUsuarios'       => $rowTbl['idUsuarios'],
                'nombre_usuario'   => $rowTbl['nombre_usuario'],
                'correo_usuario'   => $rowTbl['correo_usuario'],
                'puesto_usuario'   => $rowTbl['puesto_usuario'],
                'pass'             => $rowTbl['pass'],
                'telefono_usuario' => $rowTbl['telefono_usuario'],
                'nombre_perfil'    => $rowTbl['nombre_perfil'],
                'perfil_usuario'   => $rowTbl['perfil_usuario'],
                'status'           => $rowTbl['status'],
                'fecha_creacion'   => $rowTbl['fecha_creacion']
            );
        }

        header('Content-Type: application/json');
        echo json_encode($DtosTbl);

        $stmt->close();

    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    break;


    case 3:
        $idUsuario       = $_POST['id_usuario'];
        $nombreUsuario   = $_POST['nombre_usuario'];
        $correoUsuario   = $_POST['correo_usuario'];
        $puestoUsuario   = $_POST['puesto_usuario'];
        $telefonoUsuario = $_POST['telefono_usuario'];
        $perfilUsuario   = $_POST['perfil_usuario'];
        $status          = $_POST['status'];
        $passwordUsuario = $_POST['password_usuario'] ?? '';

        if ($passwordUsuario) {
            $passwordHash = password_hash($passwordUsuario, PASSWORD_DEFAULT);
        } else {
            $queryGetPass = "SELECT pass FROM usuarios WHERE idUsuarios = ? AND proyecto = ?";
            $stmtGetPass = $conn->prepare($queryGetPass);
            if ($stmtGetPass === false) {
                die("Error en la preparación de consulta: " . $conn->error);
            }
            $stmtGetPass->bind_param("is", $idUsuario, $proyecto);
            $stmtGetPass->execute();
            $resultPass = $stmtGetPass->get_result();
            $passwordHash = $resultPass->fetch_row()[0];
            $stmtGetPass->close();
        }

        try {
            $query = "
                UPDATE usuarios
                SET 
                    nombre_usuario   = ?,
                    correo_usuario   = ?,
                    puesto_usuario   = ?,
                    telefono_usuario = ?,
                    perfil_usuario   = ?,
                    status           = ?,
                    pass             = ?
                WHERE idUsuarios = ? AND proyecto = ?;
            ";

            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param(
                "sssssisiss",
                $nombreUsuario,
                $correoUsuario,
                $puestoUsuario,
                $telefonoUsuario,
                $perfilUsuario,
                $status,
                $passwordHash,
                $idUsuario,
                $proyecto
            );

            $stmt->execute();

            $descripcion = 'Ha editado al usuario: ' . $nombreUsuario . ' con correo: ' . $correoUsuario . ' ID: ' . $idUsuario;

            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
            $stmtLog = $conn->prepare($queryLog);
            if ($stmtLog === false) {
                die("Error en la preparación del log: " . $conn->error);
            }
            $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
            $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El registro se editó correctamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/usuarios.php';
                        });
                    }
                  </script>";

            $stmt->close();
            $stmtLog->close();

        } catch (Exception $e) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al editar el registro',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/usuarios.php';
                        });
                    }
                  </script>";
        }
        break;

case 4:
    $id_usuario = $_GET['id']; // ID del usuario a eliminar
    
    try {
        // 1️⃣ Obtener datos del usuario
        $queryGetUser = "SELECT nombre_usuario, correo_usuario, idUsuarios FROM usuarios WHERE idUsuarios = ?";
        $stmtGetUser = $conn->prepare($queryGetUser);
        if ($stmtGetUser === false) {
            throw new Exception("Error en la preparación de la consulta de usuario: " . $conn->error);
        }
        $stmtGetUser->bind_param("i", $id_usuario);
        $stmtGetUser->execute();
        $resultUser = $stmtGetUser->get_result();
        $userData = $resultUser->fetch_assoc();

        if ($userData) {
            // 2️⃣ Desactivar usuario (borrado lógico)
            $queryUpdate = "UPDATE usuarios SET status = 0 WHERE idUsuarios = ?";
            $stmtUpdate = $conn->prepare($queryUpdate);
            if ($stmtUpdate === false) {
                throw new Exception("Error en la preparación de la actualización: " . $conn->error);
            }
            $stmtUpdate->bind_param("i", $id_usuario);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            // 3️⃣ Preparar descripción del log
            $descripcion = 'Eliminó el usuario ' . $userData['nombre_usuario'] .
                           ' con correo: ' . $userData['correo_usuario'] .
                           ' ID: ' . $userData['idUsuarios'];
        } else {
            // Usuario no encontrado
            $descripcion = "Intentó eliminar un usuario que no existe o no pertenece al proyecto.";
        }

        $stmtGetUser->close();

        // 4️⃣ Guardar log
        $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
        $stmtLog = $conn->prepare($queryLog);
        if ($stmtLog === false) {
            throw new Exception("Error en la preparación del log: " . $conn->error);
        }
        $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
        $stmtLog->execute();
        $stmtLog->close();

        // 5️⃣ Mensaje SweetAlert
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script type='text/javascript'>
                window.onload = function() {
                    Swal.fire({
                        title: 'Éxito',
                        text: 'El usuario se eliminó correctamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        window.location.href = '../Views/usuarios.php';
                    });
                }
              </script>";

    } catch (Exception $e) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script type='text/javascript'>
                window.onload = function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo completar la acción. Error: " . addslashes($e->getMessage()) . "',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        window.location.href = '../Views/usuarios.php';
                    });
                }
              </script>";
    }
    break;


    case 5:
        $id_cyc         = $_GET['id'];
        $status_inicial = $_GET['status'];

        try {
            $nuevo_status = ($status_inicial === '1') ? 2 : 1;
            $query = "UPDATE usuarios SET status = ? WHERE idUsuarios = ? AND proyecto = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iis", $nuevo_status, $id_cyc, $proyecto);
            $stmt->execute();

            $queryGetUser = "SELECT nombre_usuario, correo_usuario, idUsuarios FROM usuarios WHERE idUsuarios = ? AND proyecto = ?";
            $stmtGetUser = $conn->prepare($queryGetUser);
            $stmtGetUser->bind_param("is", $id_cyc, $proyecto);
            $stmtGetUser->execute();
            $resultUser = $stmtGetUser->get_result();
            $userData = $resultUser->fetch_assoc();

            if ($status_inicial === '1') {
                $descripcion = 'Desactivó al usuario ' . $userData['nombre_usuario'] . ' con correo: ' .  $userData['correo_usuario'] . ' ID: ' . $userData['idUsuarios'];
                $icon = 'info';
                $text = 'Se desactivó al usuario.';
            } else {
                $descripcion = 'Activó al usuario ' . $userData['nombre_usuario'] . ' con correo: ' .  $userData['correo_usuario'] . ' ID: ' . $userData['idUsuarios'];
                $icon = 'info';
                $text = 'Se activó al usuario.';
            }

            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description, proyecto) VALUES (NOW(), ?, ?, ?, ?)";
            $stmtLog = $conn->prepare($queryLog);
            $stmtLog->bind_param("isss", $id_usuario, $nombre_usuario_login, $descripcion, $proyecto);
            $stmtLog->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Información',
                            text: '$text',
                            icon: '$icon',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/usuarios.php';
                        });
                    }
                  </script>";

            $stmt->close();
            $stmtGetUser->close();
            $stmtLog->close();

        } catch (Exception $e) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script type='text/javascript'>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo cambiar el estado',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = '../Views/usuarios.php';
                        });
                    }
                  </script>";
        }
        break;

    case 6:
        $id_cyc = $_GET['id'];
        try {
            $query = "SELECT * FROM usuarios WHERE idUsuarios = ? AND proyecto = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("is", $id_cyc, $proyecto);
            $stmt->execute();
            $result = $stmt->get_result();

            $resultado = $result->fetch_assoc();

            header('Content-Type: application/json');
            echo json_encode($resultado);

            $stmt->close();

        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
?>
