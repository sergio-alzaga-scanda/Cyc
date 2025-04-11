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

$fechaActual     = date("Y-m-d H:i:s");
$fechaHoraActual = $fechaActual;

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;



switch ($accion) {
    case 1:
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Recibir datos del formulario
            $nombre_usuario   = $_POST['nombre_usuario'];
            $correo_usuario   = $_POST['correo_usuario'];
            
            $puesto_usuario   = $_POST['puesto_usuario'];
            $telefono_usuario = $_POST['telefono_usuario'];
            $perfil_usuario   = $_POST['perfil_usuario'];
            $status           = $_POST['status'];
            $fecha_creacion   = $_POST['fecha_creacion'];

            // Preparar la consulta de inserción
            $query = "
            INSERT INTO [contingencias].[dbo].[usuarios] (
                [nombre_usuario], 
                [correo_usuario], 
                [pass], 
                [puesto_usuario], 
                [telefono_usuario], 
                [perfil_usuario], 
                [status]
                
            ) VALUES (
                :nombre_usuario, 
                :correo_usuario, 
                'pass'
                :puesto_usuario, 
                :telefono_usuario, 
                :perfil_usuario, 
                :status
               
            )";

            // Preparar la sentencia SQL
            $stmt = $conn->prepare($query);

            // Enlazar los parámetros
            $stmt->bindParam(':nombre_usuario', $nombre_usuario);
            $stmt->bindParam(':correo_usuario', $correo_usuario);
            $stmt->bindParam(':pass', 'contra12345');
            $stmt->bindParam(':puesto_usuario', $puesto_usuario);
            $stmt->bindParam(':telefono_usuario', $telefono_usuario);
            $stmt->bindParam(':perfil_usuario', $perfil_usuario);
            $stmt->bindParam(':status', $status);
    

            // Ejecutar la consulta
            if ($stmt->execute()) {

                // Insertar en la tabla logs 
                $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                             VALUES (GETDATE(), :user_id, :name_user, :description)";
                $stmtLog = $conn->prepare($queryLog);
                $stmtLog->bindParam(':user_id', $id_usuario);
                $stmtLog->bindParam(':name_user', $nombre_usuario_login);
                $descripcion = 'Ha registrado al usuario: ' . $nombre_usuario . ' con correo: ' . $correo_usuario ;
                $stmtLog->bindParam(':description', $descripcion);
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
                                                window.location.href = '../Views/usuarios.php'; // 
                                            });
                                        }
                                      </script>";
    } else {
        echo "Error al crear el usuario";
    }
}
break;
    
    case 2:
    $DtosTbl = array();

    try {
        // Definir la nueva consulta
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
            p.nombre_perfil  -- Obtener el nombre del perfil de la tabla perfil
        FROM 
            usuarios AS u
        JOIN 
            perfil AS p ON u.perfil_usuario = p.id_Perfil  -- Realizar el JOIN con la tabla perfil
        WHERE 
            u.status > 0
        ORDER BY 
            u.fecha_creacion DESC;
        ";

        // Preparar y ejecutar la consulta usando PDO
        $stmt = $conn->prepare($queryTbl);
        $stmt->execute();

        // Obtener los resultados y prepararlos
        while ($rowTbl = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Formatear la fecha_creacion para datetime-local si es necesario
            if ($rowTbl['fecha_creacion']) {
                // Quitar la parte de los milisegundos
                $fechaSinMilisegundos = substr($rowTbl['fecha_creacion'], 0, 19); // '2025-01-21 16:55:00'

                // Intentar crear el objeto DateTime con el formato adecuado
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $fechaSinMilisegundos);
                if ($date) {
                    $rowTbl['fecha_creacion'] = $date->format('d-m-Y H:i'); // Formato para datetime-local
                } else {
                    // Si el formato es inválido
                    $rowTbl['fecha_creacion'] = 'Fecha inválida';
                }

            }

            // Preparar los datos para la respuesta
           $DtosTbl[] = array(
                'idUsuarios'       => $rowTbl['idUsuarios'],
                'nombre_usuario'   => $rowTbl['nombre_usuario'],
                'correo_usuario'   => $rowTbl['correo_usuario'],
                'puesto_usuario'   => $rowTbl['puesto_usuario'],
                'pass'             => $rowTbl['pass'], // Asegúrate de enviar la contraseña
                'telefono_usuario' => $rowTbl['telefono_usuario'],
                'nombre_perfil'    => $rowTbl['nombre_perfil'],
                'perfil_usuario'   => $rowTbl['perfil_usuario'], // Mostrar el perfil_usuario
                'status'           => $rowTbl['status']
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
    $idUsuario       = $_POST['id_usuario'];
    $nombreUsuario   = $_POST['nombre_usuario'];
    $correoUsuario   = $_POST['correo_usuario'];
    $puestoUsuario   = $_POST['puesto_usuario'];
    $telefonoUsuario = $_POST['telefono_usuario'];
    $perfilUsuario   = $_POST['perfil_usuario'];
    $status          = $_POST['status'];
    
    // Verificar si se ha enviado una nueva contraseña
    $passwordUsuario = isset($_POST['password_usuario']) ? $_POST['password_usuario'] : '';
    
    // Si se envió una nueva contraseña, se debe actualizar
    if ($passwordUsuario) {
        // Encriptar la contraseña si se ha proporcionado una nueva
        $passwordHash = password_hash($passwordUsuario, PASSWORD_DEFAULT);
    } else {
        // Si no se envió contraseña, se mantiene la contraseña actual (asegúrate de que esté pasando la contraseña actual)
        // Supongamos que ya tienes la contraseña actual en la base de datos, es necesario recuperarla
        // Por ejemplo, recuperar la contraseña actual (si es que la necesitas)
        $queryGetPass    = "SELECT pass FROM usuarios WHERE idUsuarios = :idUsuario";
        $stmtGetPass     = $conn->prepare($queryGetPass);
        $stmtGetPass->bindParam(':idUsuario', $idUsuario);
        $stmtGetPass->execute();
        $currentPassword = $stmtGetPass->fetchColumn();
        $passwordHash    = $currentPassword;  // Mantener la contraseña actual
    }

    try {
        // Consulta SQL para actualizar el usuario
        $query = "
            UPDATE usuarios
            SET 
                nombre_usuario   = :nombre_usuario,
                correo_usuario   = :correo_usuario,
                puesto_usuario   = :puesto_usuario,
                telefono_usuario = :telefono_usuario,
                perfil_usuario   = :perfil_usuario,
                status           = :status,
                pass = :pass
            WHERE idUsuarios = :idUsuario;
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre_usuario', $nombreUsuario);
        $stmt->bindParam(':correo_usuario', $correoUsuario);
        $stmt->bindParam(':puesto_usuario', $puestoUsuario);
        $stmt->bindParam(':telefono_usuario', $telefonoUsuario);
        $stmt->bindParam(':perfil_usuario', $perfilUsuario);
        $stmt->bindParam(':status', $status);
        
        $stmt->bindParam(':idUsuario', $idUsuario);
        
        // Ejecutar la actualización
        $stmt->execute();

        // Insertar en la tabla logs 
        $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                     VALUES (GETDATE(), :user_id, :name_user, :description)";
        $stmtLog = $conn->prepare($queryLog);
        $stmtLog->bindParam(':user_id', $id_usuario);
        $stmtLog->bindParam(':name_user', $nombre_usuario_login);
        $descripcion = 'Ha editado al usuario: ' . $nombreUsuario . ' con correo: ' . $correoUsuario . ' ID: ' . $idUsuario ;
        $stmtLog->bindParam(':description', $descripcion);
        $stmtLog->execute();

        // Mostrar alerta de éxito
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script type='text/javascript'>
                window.onload = function() {
                    Swal.fire({
                        title: 'Éxito',
                        text: 'El registro se editó correctamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        window.location.href = '../Views/usuarios.php'; // Redirige a la página de éxito
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
                        text: 'Ocurrió un error al editar el registro',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        window.location.href = '../Views/usuarios.php'; // Redirige a la página de error
                    });
                }
              </script>";
    }
    break;

        case 4:
         $id_cyc = $_GET['id'];
             try {
                $query = "UPDATE usuarios 
                          SET status = 0
                          WHERE idUsuarios = :id_cyc";

                $stmt = $conn->prepare($query);
                $stmt->execute([
                    
                    ':id_cyc' => $id_cyc
                ]);

                $queryGetPass = "SELECT nombre_usuario, correo_usuario, idUsuarios FROM usuarios WHERE idUsuarios = :id_cyc";
                $stmtGetPass = $conn->prepare($queryGetPass);
                $stmtGetPass->bindParam(':id_cyc', $id_cyc);
                $stmtGetPass->execute();

                if ($stmtGetPass->execute()) {
                    $stmtGetData = $stmtGetPass->fetch(PDO::FETCH_ASSOC);

                    // Insertar en la tabla logs 
                    $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                                 VALUES (GETDATE(), :user_id, :name_user, :description)";
                    $stmtLog = $conn->prepare($queryLog);
                    $stmtLog->bindParam(':user_id', $id_usuario);
                    $stmtLog->bindParam(':name_user', $nombre_usuario_login);
                    $descripcion = 'Eliminò el usuario ' . $stmtGetData['nombre_usuario'] . ' con correo: ' .  $stmtGetData['correo_usuario'] . ' ID: ' . $stmtGetData['idUsuarios'] ;
                    $stmtLog->bindParam(':description', $descripcion);
                    $stmtLog->execute();
                }

                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script type='text/javascript'>
                                window.onload = function() {
                                    Swal.fire({
                                        title: 'Éxito',
                                        text: 'El usuario se eliminó correctamente.',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    }).then(function() {
                                        window.location.href = '../Views/usuarios.php'; // Redirige a la página de éxito
                                    });
                                }
                              </script>";
            } catch (PDOException $e) {
               echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script type='text/javascript'>
                                window.onload = function() {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'No se pudo eliminar el usuario',
                                        icon: 'Error',
                                        confirmButtonText: 'Aceptar'
                                    }).then(function() {
                                        window.location.href = '../Views/usuarios.php'; // Redirige a la página de éxito
                                    });
                                }
                              </script>";
            }
            break;
        case 5:
            

             $id_cyc         = $_GET['id'];
             $status_inicial = $_GET['status'];
            
             try {
                if ($status_inicial === '1') {
                    $nuevo_status = 2;
                }else{
                    $nuevo_status = 1;
                }
                $query = "UPDATE usuarios 
                          SET status = :nuevo_status
                          WHERE idUsuarios = :id_cyc";

                $stmt = $conn->prepare($query);
                $stmt->execute([
                    ':nuevo_status' => $nuevo_status,
                    ':id_cyc'       => $id_cyc
                ]);
                if ($status_inicial === '1') {

                    $queryGetPass = "SELECT nombre_usuario, correo_usuario, idUsuarios FROM usuarios WHERE idUsuarios = :id_cyc";
                    $stmtGetPass = $conn->prepare($queryGetPass);
                    $stmtGetPass->bindParam(':id_cyc', $id_cyc);
                    $stmtGetPass->execute();

                    if ($stmtGetPass->execute()) {
                        $stmtGetData = $stmtGetPass->fetch(PDO::FETCH_ASSOC);

                        // Insertar en la tabla logs 
                        $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                                     VALUES (GETDATE(), :user_id, :name_user, :description)";
                        $stmtLog = $conn->prepare($queryLog);
                        $stmtLog->bindParam(':user_id', $id_usuario);
                        $stmtLog->bindParam(':name_user', $nombre_usuario_login);
                        $descripcion = 'Desactivo al usuario ' . $stmtGetData['nombre_usuario'] . ' con correo: ' .  $stmtGetData['correo_usuario'] . ' ID: ' . $stmtGetData['idUsuarios'] ;
                        $stmtLog->bindParam(':description', $descripcion);
                        $stmtLog->execute();
                    }

                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script type='text/javascript'>
                                window.onload = function() {
                                    Swal.fire({
                                        title: 'Éxito',
                                        text: 'Se desactivo al usuario.',
                                        icon: 'info',
                                        confirmButtonText: 'Aceptar'
                                    }).then(function() {
                                        window.location.href = '../Views/usuarios.php'; // Redirige a la página de éxito
                                    });
                                }
                              </script>";
                }else{

                    $queryGetPass = "SELECT nombre_usuario, correo_usuario, idUsuarios FROM usuarios WHERE idUsuarios = :id_cyc";
                    $stmtGetPass = $conn->prepare($queryGetPass);
                    $stmtGetPass->bindParam(':id_cyc', $id_cyc);
                    $stmtGetPass->execute();

                    if ($stmtGetPass->execute()) {
                        $stmtGetData = $stmtGetPass->fetch(PDO::FETCH_ASSOC);

                        // Insertar en la tabla logs 
                        $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                                     VALUES (GETDATE(), :user_id, :name_user, :description)";
                        $stmtLog = $conn->prepare($queryLog);
                        $stmtLog->bindParam(':user_id', $id_usuario);
                        $stmtLog->bindParam(':name_user', $nombre_usuario_login);
                        $descripcion = 'Activó al usuario ' . $stmtGetData['nombre_usuario'] . ' con correo: ' .  $stmtGetData['correo_usuario'] . ' ID: ' . $stmtGetData['idUsuarios'] ;
                        $stmtLog->bindParam(':description', $descripcion);
                        $stmtLog->execute();
                    }

                     echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                                  <script type='text/javascript'>
                                    window.onload = function() {
                                        Swal.fire({
                                            title: 'Éxito',
                                            text: 'Se activo al usuario.',
                                            icon: 'info',
                                            confirmButtonText: 'Aceptar'
                                        }).then(function() {
                                            window.location.href = '../Views/usuarios.php'; // Redirige a la página de éxito
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
                                            text: 'No se pudo activar la crisis/contingencia',
                                            icon: 'Error',
                                            confirmButtonText: 'Aceptar'
                                        }).then(function() {
                                            window.location.href = '../Views/cyc.php'; // Redirige a la página de éxito
                                        });
                                    }
                                  </script>";
                }
            
        break;    

        default:
            echo "Acción no reconocida.";
    }


