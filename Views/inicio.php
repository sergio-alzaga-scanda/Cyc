<?php
// session_start();
// if (!$_SESSION['usuario']) {
//     header("Location: ../index.php"); 
// }
$menu = 1;
?>

<html lang="es">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estamos Trabajando</title>
</head>
<body>

<?php 
include("menu.php");
?>

<div class="container mt-5">
    <div class="text-center">
        <img src="../img/logo.png" alt="Logo" width="10%" class="mb-4">
        <h1>¡Estamos trabajando en ello!</h1>
        <p>El sitio se encuentra en mantenimiento. Estamos mejorando la experiencia para ti.</p>
        <p>Por favor, vuelve más tarde.</p>

        
    </div>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
