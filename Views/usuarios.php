<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
}
$menu = 4;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="shortcut icon" href="../img/logo.png">
    <link rel="icon" href="../img/logo.png">
</head>
<body>

<?php 
include("menu.php");
include("../Modals/modalNuevoUsuario.php");
include("../Modals/modalEditarUsuario.php");
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" class="btn-agregar" data-bs-toggle="modal" data-bs-target="#NuevoUsuario">
            Agregar &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="icono-plus">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                </svg>
            </span>
        </button>
        <img class="kenos-logo" width="6%" src="../img/logo.png">
    </div>

    <div class="filters-container mb-3">
        <div>
            <label for="searchText" class="form-label">Buscar</label>
            <input type="text" id="searchText" size="50" class="form-control" placeholder="Buscar...">
        </div>
        <div class="filter-group-btn">
            <button type="button" id="resetFiltersBtn">
                <img src="../iconos/limpiar.png" alt="Eliminar Filtros">
            </button>
        </div>
    </div>

    <div class="card-body">
        <table id="usuariosTable" class="table-custom table table-striped table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Puesto</th>
                    <th>Perfil</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Datos por AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Script para evitar que el modal se abra automáticamente si hay #NuevoUsuario en la URL -->
<script>
    if (window.location.hash === '#NuevoUsuario') {
        history.replaceState(null, null, 'usuario.php');
    }
</script>

<!-- Scripts -->
<script src="../Js/DTUsuarios.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

</body>
</html>
