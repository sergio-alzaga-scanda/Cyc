<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Incluir SweetAlert -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<title>Login</title>
</head>
<body>
<?php
include("bd.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $correo_usuario = $_POST['correo'];
    $pass           = $_POST['pass'];
    
    // Realizar la consulta usando PDO (para evitar inyección SQL)
    $queryTbl = "SELECT * FROM usuarios WHERE correo_usuario = :correo_usuario AND pass = :pass";
    $stmt = $conn->prepare($queryTbl);
    
    // Vincular los parámetros
    $stmt->bindParam(':correo_usuario', $correo_usuario);
    $stmt->bindParam(':pass', $pass);
    
   if ($stmt->execute()) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $id_usuario     = $user['idUsuarios'];
            $nombre_usuario = $user['nombre_usuario'];
        }
    }

    
    if ($user) {
        session_start();
        $_SESSION['usuario']        = $id_usuario;
        $_SESSION['nombre_usuario'] = $nombre_usuario;

        // // Insertar registro en la tabla logs
        // $queryLog = "INSERT INTO logs (fecha, user_id, name_user, description) 
        //              VALUES (GETDATE(), :user_id, :name_user, :description)";
        // $stmtLog = $conn->prepare($queryLog);
        // $descripcion = 'Inicio sesión';

        // $stmtLog->bindParam(':user_id', $id_usuario, PDO::PARAM_INT);
        // $stmtLog->bindParam(':name_user', $nombre_usuario, PDO::PARAM_STR);
        // $stmtLog->bindParam(':description', $descripcion, PDO::PARAM_STR);
        // $stmtLog->execute();



        header("Location: ../Views/dashboard.php");
		exit();
    } else {
        // Si los datos son incorrectos, mostrar mensaje de error y regresar al login
        
        echo "<script>
                Swal.fire({
                    title: 'Error',
                    text: 'Correo o contraseña incorrectos.',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 1000 
                }).then(() => {
                    window.location.href = '../index.php';
                });
              </script>";
    }
}
?>

</body>
</html>
