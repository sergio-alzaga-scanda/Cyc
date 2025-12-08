<?php
include("bd.php"); // conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo_usuario = $_POST['correo'];
    $pass = $_POST['pass'];

    // Consultar al usuario por correo
    $queryTbl = "SELECT * FROM usuarios WHERE correo_usuario = ?";
    $stmt = $conn->prepare($queryTbl);

    if ($stmt) {
        $stmt->bind_param("s", $correo_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $stored_pass = $user['pass'];
            $password_correct = false;

            // Verificar si es hash (longitud típica >= 60 caracteres)
            if (strlen($stored_pass) >= 60 && password_verify($pass, $stored_pass)) {
                $password_correct = true;
            } 
            // Si es texto plano, comparar directamente
            elseif ($pass === $stored_pass) {
                $password_correct = true;

                // Convertir contraseña a hash y actualizar base de datos
                $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE usuarios SET pass = ? WHERE idUsuarios = ?");
                $update->bind_param("si", $hashed_pass, $user['idUsuarios']);
                $update->execute();
                $update->close();
            }

            if ($password_correct) {
                // Login exitoso
                session_start();
                $_SESSION['usuario'] = $user['idUsuarios'];
                $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
                $_SESSION['correo_usuario'] = $user['correo_usuario'];
                $_SESSION['proyecto'] = $user['proyecto'];
                $_SESSION['perfil'] = $user['perfil_usuario'];

                header("Location: ../Views/dashboard.php");
                exit();
            } else {
                mostrarError();
            }
        } else {
            mostrarError();
        }

        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta.";
    }
}

$conn->close();

// Función para mostrar SweetAlert con error
function mostrarError() {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            title: 'Error',
            text: 'Correo o contraseña incorrectos.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            window.location.href = '../index.php';
        });
    </script>
    </body>
    </html>";
    exit();
}
?>
