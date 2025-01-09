<?php
session_start();
if ($_SESSION['usuario'] < 0) {
    header("Location: ../index.php");
}
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
    <title>Dashboard</title>
</head>
<body>

<?php 
include("menu.php");
include("../Modals/modalNuevaCrisis.php");
include("../Modals/modalEditarCrisis.php");
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
            Abrir Formulario
        </button>
        <img class="kenos-logo" width="6%" src="../img/logo.png">
    </div>

    <div class="mb-3">
        <label for="filterDate" class="form-label">Filtrar por fecha de activación</label>
        <input type="date" id="filterDate" class="form-control">
    </div>

    <button type="button" class="btn btn-secondary mb-3" id="resetFiltersBtn">Eliminar Filtros</button>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="m-0">Tabla de Crisis</h5>
        </div>
        <div class="card-body">
            <table id="crisisTable" class="table table-striped table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>No. Ticket</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Ubicación</th>
                        <th>Fecha Activación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se llenarán las filas con datos mediante AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../js/DTCrisis.js"></script>
<!-- Scripts de Bootstrap y DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

</body>
</html>
