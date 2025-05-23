<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
}
include("../Controllers/bd.php");
$id_usuario     = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_usuario'];
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fechaActual     = date("Y-m-d H:i:s");
$fechaHoraActual = $fechaActual;

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;


switch ($accion) {
    case 1:
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Datos enviados desde el formulario
            $no_ticket = $_POST['no_ticket'] ?? '';
            $nombre    = $_POST['nombre'] ?? '';
            $fecha     = $_POST['fecha_programacion'] ?? null; // Puede ser NULL

            // Validar y formatear la fecha de la programación
            if ($fecha) {
                $status       = 2;
                $fecha_string = trim($fecha);  // Elimina cualquier espacio extra
                try {
                    $fecha_obj          = new DateTime($fecha_string);
                    $fecha_programacion = $fecha_obj->format('Y-m-d H:i:s'); // Formato para SQL Server
                } catch (Exception $e) {
                    echo "Error al procesar la fecha: " . $e->getMessage();
                    exit; // Salir si la fecha no es válida
                }
            } else {
                $status = 1;
                $fecha_programacion = null; // Si no se proporciona, se deja como NULL
            }

            $criticidad          = $_POST['criticidad'] ?? '';
            $tipo                = $_POST['tipo'] ?? '';
            $ubicacion           = $_POST['ubicacion'] ?? '';
            $ivr_texto           = $_POST['ivr'] ?? '';
            $redaccion_canales   = $_POST['redaccion_canales'] ?? '';
            $proyecto            = $_POST['proyecto'] ?? '';
            
            $canales             = $_POST['canal'] ?? []; // Array
            $bots                = $_POST['bot'] ?? []; // Array
            $mismo_canal         = isset($_POST['mismo-canal']) ? 'Sí' : 'No';
            $canal_digital_texto = $_POST['canal-digital-texto'] ?? '';

            // Convertir los arrays a formato JSON
            $canales_json = json_encode($canales);
            $bots_json    = json_encode($bots);

            // Verificar si el no_ticket ya existe
            try {
                // Consulta para verificar si el no_ticket ya existe
                $query_check = "SELECT COUNT(*) FROM [contingencias].[dbo].[cyc] WHERE no_ticket = :no_ticket AND status_cyc IN (1, 2);";
                $stmt_check = $conn->prepare($query_check);
                $stmt_check->bindParam(':no_ticket', $no_ticket);
                $stmt_check->execute();

                $ticket_exists = $stmt_check->fetchColumn();

                if ($ticket_exists > 0) {
                    // Si el ticket ya existe, mostrar la alerta con SweetAlert y redirigir a la página anterior
                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                          <script type='text/javascript'>
                            window.onload = function() {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'El número de ticket ya existe.',
                                    icon: 'error',
                                    confirmButtonText: 'Cerrar'
                                }).then(function() {
                                    window.history.back(); // Redirige a la página anterior
                                });
                            }
                          </script>";
                    exit; // Detener la ejecución del script
                } else {
                    // Si el ticket no existe, proceder con el INSERT
                    $query = "
                        INSERT INTO [contingencias].[dbo].[cyc] (
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
                        ) VALUES (
                            :nombre,
                            :no_ticket,
                            :categoria_cyc,
                            :tipo_cyc,
                            :ubicacion_cyc,
                            :redaccion_cyc,
                            :canal_cyc,
                            :bot_cyc,
                            :redaccion_canal_cyc,
                            GETDATE(), -- Fecha de registro
                            :status_cyc,
                            :fecha_programacion,
                            :id_usuario,
                            :redaccion_canales,
                            :proyecto
                        )
                    ";

                    // Preparar la consulta
                    $stmt = $conn->prepare($query);

                    // Vincular los valores
                    $stmt->bindParam(':nombre', $nombre);
                    $stmt->bindParam(':no_ticket', $no_ticket);
                    $stmt->bindParam(':categoria_cyc', $criticidad);
                    $stmt->bindParam(':tipo_cyc', $tipo);
                    $stmt->bindParam(':ubicacion_cyc', $ubicacion);
                    $stmt->bindParam(':redaccion_cyc', $ivr_texto);
                    $stmt->bindParam(':canal_cyc', $canales_json);
                    $stmt->bindParam(':bot_cyc', $bots_json);
                    $stmt->bindParam(':redaccion_canal_cyc', $canal_digital_texto);
                    $stmt->bindParam(':status_cyc', $status);
                    $stmt->bindParam(':fecha_programacion', $fecha_programacion);
                    $stmt->bindParam(':redaccion_canales', $redaccion_canales);
                    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                    $stmt->bindParam(':proyecto', $proyecto);

                    // Ejecutar la consulta
                    if ($stmt->execute()) {
                        // Mostrar mensaje de éxito con SweetAlert y redirigir a la página de éxito
                        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script type='text/javascript'>
                                window.onload = function() {
                                    Swal.fire({
                                        title: 'Éxito',
                                        text: 'El ticket se ha registrado correctamente.',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    }).then(function() {
                                        window.location.href = '../Views/cyc.php'; // Redirige a la página de éxito
                                    });
                                }
                              </script>";

                        // // Insertar en la tabla logs 
                        // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                        //              VALUES (GETDATE(), :user_id, :name_user, :description)";
                        // $stmtLog = $conn->prepare($queryLog);
                        // $stmtLog->bindParam(':user_id', $id_usuario);
                        // $stmtLog->bindParam(':name_user', $nombre_usuario);
                        // $descripcion = 'El ticket se ha registrado correctamente, numero de ticket: ' . $no_ticket ;
                        // $stmtLog->bindParam(':description', $descripcion);
                        // $stmtLog->execute();


                    } else {
                        echo "Error al insertar el registro.";
                    }
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            break;
        }
    
case 2:
    $DtosTbl = array();

    try {
        // Definir la nueva consulta
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
            ui.nombre_ubicacion_ivr AS nombre_ubicacion,  -- Columna adicional para el nombre de la ubicación
            CASE 
                WHEN c.fecha_programacion > c.fecha_registro_cyc THEN c.fecha_programacion
                ELSE c.fecha_registro_cyc
            END AS fecha_activacion,
            p.nombre_proyecto  -- Nueva columna para el nombre del proyecto
        FROM 
            cyc AS c
        JOIN 
            cat_crisis AS cc ON c.categoria_cyc = cc.id
        LEFT JOIN 
            ubicacion_ivr AS ui ON c.ubicacion_cyc = ui.id_ubicacion_ivr  -- Relacionar con la tabla de ubicaciones
        LEFT JOIN 
            cat_proyectos AS p ON c.proyecto = p.id_proyecto  -- Relacionar con la tabla de proyectos
        WHERE 
            c.status_cyc > 0
        ORDER BY 
            c.fecha_registro_cyc DESC;
        ";

        // Ejecutar la consulta usando PDO
        $stmt = $conn->query($queryTbl);

        if ($stmt === false) {
            echo json_encode(['error' => $conn->errorInfo()]);
            exit;
        }

        // Obtener los resultados y prepararlos
        while ($rowTbl = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Formatear la fecha_activacion para datetime-local
            if ($rowTbl['fecha_activacion']) {
                // Quitar la parte de los milisegundos
                $fechaSinMilisegundos = substr($rowTbl['fecha_activacion'], 0, 19); // '2025-01-21 16:55:00'

                // Intentar crear el objeto DateTime con el formato adecuado
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $fechaSinMilisegundos);
                if ($date) {
                    $rowTbl['fecha_activacion'] = $date->format('d-m-Y H:i'); // Formato para datetime-local
                } else {
                    // Si el formato es inválido
                    $rowTbl['fecha_activacion'] = 'Fecha inválida';
                }
            }

            $DtosTbl[] = array(
                'id_cyc'           => $rowTbl['id_cyc'],
                'no_ticket'        => $rowTbl['no_ticket'],
                'status_cyc'       => $rowTbl['status_cyc'],
                'categoria_nombre' => $rowTbl['categoria_nombre'], 
                'tipo_cyc'         => $rowTbl['tipo_cyc'], 
                'nombre_ubicacion' => $rowTbl['nombre_ubicacion'],
                'ubicacion_cyc'    => $rowTbl['ubicacion_cyc'],
                'fecha_activacion' => $rowTbl['fecha_activacion'],  
                'nombre_proyecto'  => $rowTbl['nombre_proyecto'],  // Agregar el nombre del proyecto
            );
        }

        // Enviar la respuesta como JSON
        header('Content-Type: application/json');
        echo json_encode($DtosTbl);
    } catch (Exception $e) {
        // Capturar cualquier error y devolverlo como JSON
        echo json_encode(['error' => $e->getMessage()]);
    }
    break;


   case 3: // Obtener datos de una crisis específica
    $id                 = $_POST['id'];
    $queryCrisisDetails = "SELECT * FROM cyc WHERE id_cyc = :id";
    $crisisDetails      = $conn->prepare($queryCrisisDetails);
    $crisisDetails->bindParam(':id', $id, PDO::PARAM_INT);

    if ($crisisDetails->execute()) {
    $crisisData = $crisisDetails->fetch(PDO::FETCH_ASSOC);

    // Decodificar JSON de los campos `canal_cyc` y `bot_cyc`
    $crisisData['canal_cyc'] = json_decode($crisisData['canal_cyc'], true);
    $crisisData['bot_cyc']   = json_decode($crisisData['bot_cyc'], true);

    // Formatear la fecha `fecha_programacion` para datetime-local
    if ($crisisData['fecha_programacion']) {
        // Quitar la parte de los milisegundos
        $fechaSinMilisegundos = substr($crisisData['fecha_programacion'], 0, 19); // '2025-01-21 16:55:00'

        // Intentar crear el objeto DateTime con el formato adecuado
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $fechaSinMilisegundos);
        if ($date) {
            $crisisData['fecha_programacion'] = $date->format('d/m/Y H:i'); // Formato para datetime-local
        } else {
            // Si el formato es inválido
            $crisisData['fecha_programacion'] = 'Fecha inválida';
        }
    }
    
    // Obtener el proyecto relacionado, si es necesario (esto depende de tu base de datos)
    // Aquí asumimos que `proyecto` es un campo relacionado en la base de datos
    $crisisData['proyecto']  = $crisisData['proyecto'] ?? null;  // Añadir el dato del proyecto al array
    $crisisData['no_ticket'] = $crisisData['no_ticket']; 


    // Enviar los datos en formato JSON al frontend
    echo json_encode($crisisData);

} else {
    echo json_encode(['error' => 'Error al obtener los detalles de la crisis']);
}

    break;
        case 4:
         $id_cyc = $_GET['id'];
         
             try {
                $query = "UPDATE cyc 
                          SET status_cyc = 0
                          WHERE id_cyc = :id_cyc";

                $stmt = $conn->prepare($query);
                $stmt->execute([
                    
                    ':id_cyc' => $id_cyc
                ]);

                $queryCrisisDetails = "SELECT * FROM cyc WHERE id_cyc = :id_cyc";
                $crisisDetails = $conn->prepare($queryCrisisDetails);
                $crisisDetails->bindParam(':id_cyc', $id_cyc, PDO::PARAM_INT);

                if ($crisisDetails->execute()) {
                    $crisisData = $crisisDetails->fetch(PDO::FETCH_ASSOC);

                    // // Insertar en la tabla logs 
                    // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                    //              VALUES (GETDATE(), :user_id, :name_user, :description)";
                    // $stmtLog = $conn->prepare($queryLog);
                    // $stmtLog->bindParam(':user_id', $id_usuario);
                    // $stmtLog->bindParam(':name_user', $nombre_usuario);
                    // $descripcion = 'Eliminò el número de ticket: ' . $crisisData['no_ticket'] ;
                    // $stmtLog->bindParam(':description', $descripcion);
                    // $stmtLog->execute();
                }

                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script type='text/javascript'>
                                window.onload = function() {
                                    Swal.fire({
                                        title: 'Éxito',
                                        text: 'El ticket se eliminó correctamente.',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    }).then(function() {
                                        window.location.href = '../Views/cyc.php'; // Redirige a la página de éxito
                                    });
                                }
                              </script>";
            } catch (PDOException $e) {
               echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script type='text/javascript'>
                                window.onload = function() {
                                    Swal.fire({
                                        title: 'Error aqui',
                                        text: 'No se pudo eliminar el registro',
                                        icon: 'Error',
                                        confirmButtonText: 'Aceptar'
                                    }).then(function() {
                                        window.location.href = '../Views/cyc.php'; // Redirige a la página de éxito
                                    });
                                }
                              </script>";
            }
            break;
        case 5:
            $id_cyc             = intval($_POST['id']);
            $nombre             = $_POST['nombre'];
            $categoria_cyc      = intval($_POST['categoria_edit']);
            $tipo_cyc           = intval($_POST['tipo_edit']);
            $ubicacion_cyc      = intval($_POST['ubicacion_edit']);
            $redaccion_cyc      = $_POST['ivr_edit'];
            $programar          = isset($_POST['programar']) ? 1 : 0;
            $fecha_programacion = $programar ? $_POST['fecha_programacion_2'] : null;
            $canal_cyc          = isset($_POST['canal_edit']) ? implode(',', $_POST['canal_edit']) : null;
            $bot_cyc            = isset($_POST['bot_edit']) ? implode(',', $_POST['bot_edit']) : null;
            $redaccion_canales  = $_POST['redaccion_canales_edit'];
            $edit_proyecto      = $_POST['edit_proyecto'];
            $no_ticket          = $_POST['no_ticket'];
            // Convertir fecha al formato SQL Server
            if ($fecha_programacion) {
                $fecha_programacion = str_replace('T', ' ', $fecha_programacion) . ':00';
            }

            // Validar campos requeridos
            if (empty($id_cyc) || empty($nombre) || empty($categoria_cyc) || empty($tipo_cyc) || empty($ubicacion_cyc)) {
                die('Error: Todos los campos requeridos deben completarse.');
            }

            try {
                $query = "UPDATE cyc 
                              SET nombre         = :nombre,
                              categoria_cyc      = :categoria_cyc,
                              tipo_cyc           = :tipo_cyc,
                              ubicacion_cyc      = :ubicacion_cyc,
                              redaccion_cyc      = :redaccion_cyc,
                              fecha_programacion = :fecha_programacion,
                              redaccion_canales  = :redaccion_canales,
                              proyecto           = :edit_proyecto
                          WHERE id_cyc = :id_cyc";

                $stmt = $conn->prepare($query);
                $stmt->execute([
                    ':nombre'             => $nombre,
                    ':categoria_cyc'      => $categoria_cyc,
                    ':tipo_cyc'           => $tipo_cyc,
                    ':ubicacion_cyc'      => $ubicacion_cyc,
                    ':redaccion_cyc'      => $redaccion_cyc,
                    ':fecha_programacion' => $fecha_programacion,
                    ':redaccion_canales'  => $redaccion_canales,
                    ':edit_proyecto'      => $edit_proyecto,
                    ':id_cyc'             => $id_cyc
                ]);

                // // Insertar en la tabla logs 
                // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                //              VALUES (GETDATE(), :user_id, :name_user, :description)";
                // $stmtLog = $conn->prepare($queryLog);
                // $stmtLog->bindParam(':user_id', $id_usuario);
                // $stmtLog->bindParam(':name_user', $nombre_usuario);
                // $descripcion = 'El registro se editó correctamente, numero de ticket: ' . $no_ticket ;
                // $stmtLog->bindParam(':description', $descripcion);
                // $stmtLog->execute();

                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script type='text/javascript'>
                                window.onload = function() {
                                    Swal.fire({
                                        title: 'Éxito',
                                        text: 'El registro se editó correctamente.',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    }).then(function() {
                                        window.location.href = '../Views/cyc.php'; // Redirige a la página de éxito
                                    });
                                }
                              </script>";


            } catch (PDOException $e) {
               echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script type='text/javascript'>
                                window.onload = function() {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Ocurrio un error al editar el registro',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    }).then(function() {
                                        window.location.href = '../Views/cyc.php'; // Redirige a la página de éxito
                                    });
                                }
                              </script>";
            }
            break;
            
        break;    
        case 6:
            

             $id_cyc         = $_GET['id'];
             $status_inicial = $_GET['status'];
            
             try {
                if ($status_inicial === '1') {
                    $nuevo_status = 2;
                }else{
                    $nuevo_status = 1;
                }
                $query = "UPDATE cyc 
                          SET status_cyc = :nuevo_status
                          WHERE id_cyc = :id_cyc";

                $stmt = $conn->prepare($query);
                $stmt->execute([
                    ':nuevo_status' => $nuevo_status,
                    ':id_cyc' => $id_cyc
                ]);
                if ($status_inicial === '1') {

                    $queryCrisisDetails = "SELECT * FROM cyc WHERE id_cyc = :id_cyc";
                    $crisisDetails      = $conn->prepare($queryCrisisDetails);
                    $crisisDetails->bindParam(':id_cyc', $id_cyc, PDO::PARAM_INT);

                    if ($crisisDetails->execute()) {
                        $crisisData = $crisisDetails->fetch(PDO::FETCH_ASSOC);

                        // // Insertar en la tabla logs 
                        // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                        //              VALUES (GETDATE(), :user_id, :name_user, :description)";
                        // $stmtLog = $conn->prepare($queryLog);
                        // $stmtLog->bindParam(':user_id', $id_usuario);
                        // $stmtLog->bindParam(':name_user', $nombre_usuario);
                        // $descripcion = 'Desactivo la grabación con número de ticket: ' . $crisisData['no_ticket'] ;
                        // $stmtLog->bindParam(':description', $descripcion);
                        // $stmtLog->execute();
                    }

                   echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                          <script type='text/javascript'>
                            window.onload = function() {
                                Swal.fire({
                                    title: 'Éxito',
                                    text: 'La grabación ha sido deshabilitada exitosamente.',
                                    icon: 'success',
                                    confirmButtonText: 'Aceptar',
                                    confirmButtonColor: '#4B4A4B', // Color del botón
                                    customClass: {
                                        confirmButton: 'swal2-bold-button' // Clase personalizada para el texto en negrita
                                    },
                                    didOpen: () => {
                                        // Cambiar icono de confirmación
                                       
                                    }
                                }).then(function() {
                                    window.location.href = '../Views/cyc.php'; // Redirige a la página de éxito
                                });
                            }
                          </script>";

            }else{

                $queryCrisisDetails = "SELECT * FROM cyc WHERE id_cyc = :id_cyc";
                $crisisDetails      = $conn->prepare($queryCrisisDetails);
                $crisisDetails->bindParam(':id_cyc', $id_cyc, PDO::PARAM_INT);

                if ($crisisDetails->execute()) {
                    $crisisData = $crisisDetails->fetch(PDO::FETCH_ASSOC);

                    // // Insertar en la tabla logs 
                    // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
                    //              VALUES (GETDATE(), :user_id, :name_user, :description)";
                    // $stmtLog = $conn->prepare($queryLog);
                    // $stmtLog->bindParam(':user_id', $id_usuario);
                    // $stmtLog->bindParam(':name_user', $nombre_usuario);
                    // $descripcion = 'Activó la grabación con número de ticket: ' . $crisisData['no_ticket'] ;
                    // $stmtLog->bindParam(':description', $descripcion);
                    // $stmtLog->execute();
                }

                 echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script type='text/javascript'>
                        window.onload = function() {
                            Swal.fire({
                                title: 'Éxito',
                                text: 'La grabación ha sido habilitada exitosamente.',
                                icon: 'success',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#4B4A4B', // Color del botón
                                customClass: {
                                    confirmButton: 'swal2-bold-button' // Clase personalizada para el texto en negrita
                                },
                                didOpen: () => {
                                    // Cambiar icono de confirmación
                                    
                                }
                            }).then(function() {
                                window.location.href = '../Views/cyc.php'; // Redirige a la página de éxito
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


