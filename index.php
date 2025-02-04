<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Crisis y Contingencias</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style_login.css">
</head>
<body>
    <div class="background-container">
        <img src="img/Login.png" class="bg-image" alt="Fondo">
    </div>
    <div class="container-fluid">

        <!-- Cabecera con logo e identificación -->
        <div class="row align-items-center header">
            <div class="col-2 d-flex justify-content-start align-items-center">
                <img src="img/logo.png" style="padding-left: 15%; margin-top: 15%;" width="200px" alt="Logo Kenos" class="logo">
            </div>
            <div class="col-8" style="padding-right: 20%;">
                <h1 class="bold-text">Gestión de Crisis y Contingencias</h1>
            </div>
            <div class="col-2"></div>
        </div>
        
        <!-- Panel de inicio de sesión centrado -->
        <div style="padding-bottom: 25%;" class="row d-flex justify-content-center align-items-center vh-100">
            <div class="col-md-6 d-flex justify-content-start align-items-center">
                <div class="login-panel bg-white p-5 w-75">
                    <form action="Controllers/sesion.php" method="POST" onsubmit="return validarCorreo()">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend" >
                                    <span class="input-group-text bg-white border-right-0 icon-style">
                                        <img src="iconos/Vector-10.png" height="24px" width="24px" alt="Icono Usuario" class="icon">
                                    </span>
                                </div>
                                <input type="email" class="form-control border-left-0 regular-text" name="correo" id="correo" placeholder="Correo electrónico" required>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0 icon-style">
                                        <img src="iconos/Vector-11.png" height="24px" width="24px" name="" id="" alt="Icono Contraseña" class="icon">
                                    </span>
                                </div>
                                <input type="password" class="form-control border-left-0 regular-text" name="pass" id="pass" placeholder="Contraseña" required>
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="boton-formateado">
                            <span class="texto-formateado">Ingresar</span>
                            <span><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
</svg></span>
                        </button>
                        <div class="text-left mt-3">
                            <u><a href="#" class="forgot-password regular-text" style="color: black; text-decoration: none;">Olvidé mi contraseña</a></u>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6 background"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- JavaScript para la validación del correo -->
    <script>
        function validarCorreo() {
            const correo = document.getElementById("correo");
            const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;  // Expresión regular para validar correo
            
            if (!regex.test(correo.value)) {
                correo.style.backgroundColor = "#DD4D4D";  // Cambia el color de fondo si no es un correo válido
                return false;  // Evita el envío del formulario
            } else {
                correo.style.backgroundColor = "";  // Restaura el color de fondo si es válido
                return true;  // Permite el envío del formulario
            }
        }
    </script>
</body>
</html>
