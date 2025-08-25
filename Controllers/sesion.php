<?php
include("bd.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $correo_usuario = $_POST['correo'];
    $pass = $_POST['pass'];

    // Preparar consulta con sentencias preparadas de mysqli
    $queryTbl = "SELECT * FROM usuarios WHERE correo_usuario = ? AND pass = ?";
    $stmt = $conn->prepare($queryTbl);
    
    if ($stmt) {
        $stmt->bind_param("ss", $correo_usuario, $pass); // Dos cadenas
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            session_start();
            $_SESSION['usuario'] = $user['idUsuarios'];
            $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
            $_SESSION['correo_usuario'] = $user['correo_usuario'];
            $_SESSION['proyecto'] = $user['proyecto'];
            $_SESSION['perfil'] = $user['perfil_usuario'];
            

            // Redirigir al dashboard
            header("Location: ../Views/dashboard.php");
            exit();
        } else {
            // Datos incorrectos
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

        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta.";
    }
}

$conn->close();
?>
