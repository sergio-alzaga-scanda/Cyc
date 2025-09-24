<?php 
session_start(); 
if (!$_SESSION['usuario']) { 
    header("Location: ../index.php"); 
    exit; 
} 

include("../Controllers/bd.php"); 
$id_usuario = $_SESSION['usuario']; 
$nombre_usuario_login = $_SESSION['nombre_usuario']; 
$proyecto = $_SESSION['proyecto']; 

ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL); 

$fechaActual = date("Y-m-d H:i:s"); 
$fechaHoraActual = $fechaActual; 
$accion = $_POST['accion'] ?? $_GET['accion'] ?? null; 

switch ($accion) { 
    case 1: 
        if ($_SERVER["REQUEST_METHOD"] == "POST") { 
            $nombre = $_POST['nombre']; 
            $proyecto_seleccionado = $_POST['proyecto']; // Nuevo campo proyecto
            $status = 1; 

            // INSERT con proyecto seleccionado
            $sql = "INSERT INTO ubicacion_ivr (nombre_ubicacion_ivr, status, proyecto) VALUES (?, ?, ?)"; 
            $stmt = $conn->prepare($sql); 
            if (!$stmt) { 
                die("Prepare failed: " . $conn->error); 
            } 
            $stmt->bind_param("sis", $nombre, $status, $proyecto_seleccionado); 
            
            if ($stmt->execute()) { 
                // Insert logs 
                $descripcion = 'Ha creado una Ubicación IVR de nombre: ' . $nombre . ' para el proyecto: ' . $proyecto_seleccionado; 
                $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) VALUES (NOW(), ?, ?, ?)"; 
                $stmtLog = $conn->prepare($queryLog); 
                if (!$stmtLog) { 
                    die("Prepare failed (log): " . $conn->error); 
                } 
                $stmtLog->bind_param("iss", $id_usuario, $nombre_usuario_login, $descripcion); 
                $stmtLog->execute(); 
                $stmtLog->close(); 
                
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script> <script type='text/javascript'> window.onload = function() { Swal.fire({ title: 'Éxito', text: 'Se guardó el registro correctamente.', icon: 'success', confirmButtonText: 'Aceptar' }).then(function() { window.location.href = '../Views/catalogos.php'; }); } </script>"; 
            } else { 
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script> <script type='text/javascript'> window.onload = function() { Swal.fire({ title: 'Error', text: 'Ocurrió un error al guardar el registro.', icon: 'error', confirmButtonText: 'Aceptar' }).then(function() { window.location.href = '../Views/catalogos.php'; }); } </script>"; 
            } 
            $stmt->close(); 
        } 
        $conn->close(); 
        break; 

    case 2: 
        $DtosTbl = array(); 
        try { 
            // Modificada para incluir el proyecto
            $queryTbl = "SELECT id_ubicacion_ivr, nombre_ubicacion_ivr, status, proyecto FROM ubicacion_ivr WHERE status > 0 ORDER BY nombre_ubicacion_ivr DESC"; 
            $stmt = $conn->prepare($queryTbl); 
            if (!$stmt) { 
                throw new Exception("Prepare failed: " . $conn->error); 
            } 
            $stmt->execute(); 
            $result = $stmt->get_result(); 
            
            while ($rowTbl = $result->fetch_assoc()) { 
                $DtosTbl[] = array( 
                    'id' => $rowTbl['id_ubicacion_ivr'], 
                    'nombre_ubicacion_ivr' => $rowTbl['nombre_ubicacion_ivr'], 
                    'status' => $rowTbl['status'],
                    'proyecto' => $rowTbl['proyecto']
                ); 
            } 
            
            header('Content-Type: application/json'); 
            echo json_encode($DtosTbl); 
            $stmt->close(); 
            $conn->close(); 
            
        } catch (Exception $e) { 
            echo json_encode(['error' => $e->getMessage()]); 
        } 
        break; 

    case 3: 
        $idUbicacion = $_POST['edit_id_ubicacion_ivr']; 
        $nombreUbicacion = $_POST['nombre']; 
        $proyecto_seleccionado = $_POST['proyecto']; // Nuevo campo proyecto

        try { 
            $query = "UPDATE ubicacion_ivr SET nombre_ubicacion_ivr = ?, proyecto = ? WHERE id_ubicacion_ivr = ?"; 
            $stmt = $conn->prepare($query); 
            if (!$stmt) { 
                throw new Exception("Prepare failed: " . $conn->error); 
            } 
            $stmt->bind_param("ssi", $nombreUbicacion, $proyecto_seleccionado, $idUbicacion); 
            $stmt->execute(); 
            
            // Log 
            $descripcion = 'Ha editado una Ubicación IVR de nombre: ' . $nombreUbicacion . ' con ID: ' . $idUbicacion . ' para el proyecto: ' . $proyecto_seleccionado; 
            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) VALUES (NOW(), ?, ?, ?)"; 
            $stmtLog = $conn->prepare($queryLog); 
            if (!$stmtLog) { 
                throw new Exception("Prepare failed (log): " . $conn->error); 
            } 
            $stmtLog->bind_param("iss", $id_usuario, $nombre_usuario_login, $descripcion); 
            $stmtLog->execute(); 
            $stmtLog->close(); 
            
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script> <script type='text/javascript'> window.onload = function() { Swal.fire({ title: 'Éxito', text: 'La ubicación IVR se editó correctamente.', icon: 'success', confirmButtonText: 'Aceptar' }).then(function() { window.location.href = '../Views/catalogos.php'; }); } </script>"; 
            $stmt->close(); 
            $conn->close(); 
            
        } catch (Exception $e) { 
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script> <script type='text/javascript'> window.onload = function() { Swal.fire({ title: 'Error', text: 'Ocurrió un error al editar el registro.', icon: 'error', confirmButtonText: 'Aceptar' }).then(function() { window.location.href = '../Views/catalogos.php'; }); } </script>"; 
        } 
        break; 

    case 4: 
        $id_ubicacion_ivr = $_GET['id']; 
        try { 
            $query = "UPDATE ubicacion_ivr SET status = 0 WHERE id_ubicacion_ivr = ?"; 
            $stmt = $conn->prepare($query); 
            if (!$stmt) { 
                throw new Exception("Prepare failed: " . $conn->error); 
            } 
            $stmt->bind_param("i", $id_ubicacion_ivr); 
            $stmt->execute(); 
            
            // Log 
            $descripcion = 'Ha eliminado una Ubicación IVR con ID: ' . $id_ubicacion_ivr; 
            $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) VALUES (NOW(), ?, ?, ?)"; 
            $stmtLog = $conn->prepare($queryLog); 
            if (!$stmtLog) { 
                throw new Exception("Prepare failed (log): " . $conn->error); 
            } 
            $stmtLog->bind_param("iss", $id_usuario, $nombre_usuario_login, $descripcion); 
            $stmtLog->execute(); 
            $stmtLog->close(); 
            
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script> <script type='text/javascript'> window.onload = function() { Swal.fire({ title: 'Éxito', text: 'La ubicación IVR se eliminó correctamente.', icon: 'success', confirmButtonText: 'Aceptar' }).then(function() { window.location.href = '../Views/catalogos.php'; }); } </script>"; 
            $stmt->close(); 
            $conn->close(); 
            
        } catch (Exception $e) { 
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script> <script type='text/javascript'> window.onload = function() { Swal.fire({ title: 'Error', text: 'No se pudo eliminar la ubicación IVR.', icon: 'error', confirmButtonText: 'Aceptar' }).then(function() { window.location.href = '../Views/catalogos.php'; }); } </script>"; 
        } 
        break; 

    case 5: 
        $id_ubicacion_ivr = $_GET['id']; 
        $status_actual = $_GET['status']; 

        try { 
            $nuevo_status = ($status_actual == 1) ? 2 : 1; 
            $query = "UPDATE ubicacion_ivr SET status = ? WHERE id_ubicacion_ivr = ?"; 
            $stmt = $conn->prepare($query); 
            if (!$stmt) { 
                throw new Exception("Prepare failed: " . $conn->error); 
            } 
            $stmt->bind_param("ii", $nuevo_status, $id_ubicacion_ivr); 
            
            if ($stmt->execute()) { 
                // Log de la acción 
                $accion_log = $nuevo_status == 2 ? 'desactivó' : 'activó'; 
                $descripcion = "Ha $accion_log una Ubicación IVR con ID: $id_ubicacion_ivr"; 
                
                $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) VALUES (NOW(), ?, ?, ?)"; 
                $stmtLog = $conn->prepare($queryLog); 
                if ($stmtLog) { 
                    $stmtLog->bind_param("iss", $id_usuario, $nombre_usuario_login, $descripcion); 
                    $stmtLog->execute(); 
                    $stmtLog->close(); 
                } 
                
                // Respuesta JSON para AJAX 
                header('Content-Type: application/json'); 
                echo json_encode([ 
                    'success' => true, 
                    'message' => $nuevo_status == 2 ? 'Ubicación IVR desactivada' : 'Ubicación IVR activada', 
                    'nuevo_status' => $nuevo_status 
                ]); 
            } else { 
                throw new Exception("Execute failed: " . $stmt->error); 
            } 
            
            $stmt->close(); 
            $conn->close(); 
            
        } catch (Exception $e) { 
            header('Content-Type: application/json'); 
            echo json_encode(['success' => false, 'error' => $e->getMessage()]); 
        } 
        break; 

    case 6: 
        // Nueva acción para obtener proyectos
        try { 
            $queryProyectos = "SELECT id_proyecto, nombre_proyecto FROM cat_proyectos WHERE status = 1 ORDER BY nombre_proyecto ASC"; 
            $stmt = $conn->prepare($queryProyectos); 
            if (!$stmt) { 
                throw new Exception("Prepare failed: " . $conn->error); 
            } 
            $stmt->execute(); 
            $result = $stmt->get_result(); 
            
            $proyectos = array(); 
            while ($row = $result->fetch_assoc()) { 
                $proyectos[] = array( 
                    'id_proyecto' => $row['id_proyecto'], 
                    'nombre_proyecto' => $row['nombre_proyecto'] 
                ); 
            } 
            
            header('Content-Type: application/json'); 
            echo json_encode($proyectos); 
            $stmt->close(); 
            $conn->close(); 
            
        } catch (Exception $e) { 
            echo json_encode(['error' => $e->getMessage()]); 
        } 
        break; 

    default: 
        echo "Acción no reconocida."; 
} 
?>