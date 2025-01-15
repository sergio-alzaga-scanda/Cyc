<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
}
$menu = 2;
?>

<html lang="es">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style_tablas.css">
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
        <button type="button" class="btn-agregar" data-bs-toggle="modal" data-bs-target="#loginModal">
     Agregar &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="icono-plus">+</span>
</button>

        <img class="kenos-logo" width="6%" src="../img/logo.png">
    </div>

    <!-- Filtros en columna (vertical) -->
    <div class="filters-container mb-3">
        <div style="padding-right: 15%;" >
            <label for="searchText" class="form-label">Buscar</label>
            <input type="text" id="searchText" size="50" class="form-control" placeholder="Buscar...">
        </div>

        <div class="filter-group">
            <label for="filterDate" class="form-label">Filtrar por fecha</label>
            <input type="date" id="filterDate" class="form-control">
        </div>

        <div class="filter-group">
            <label for="contingencyStatus" class="form-label">Filtrar por Tipo</label><br>
            <input type="radio" id="contingency" name="contingencyType" value="contingencia">
            <label for="contingency">Contingencia</label><br>
            <input type="radio" id="crisis" name="contingencyType" value="crisis">
            <label for="crisis">Crisis</label><br>
            <input type="radio" id="both" name="contingencyType" value="ambos">
            <label for="both">Ambos</label>
        </div>

        <div class="filter-group">
            <label for="statusType" class="form-label">Filtrar por Estado</label><br>
            <input type="radio" id="activo" name="statusType" value="1">
            <label for="activo">Activo</label><br>
            <input type="radio" id="programado" name="statusType" value="3">
            <label for="programado">Programado</label><br>
            <input type="radio" id="inactivo" name="statusType" value="2">
            <label for="inactivo">Inactivo</label><br>
            <input type="radio" id="ambosStatus" name="statusType" value="Todos">
            <label for="ambosStatus">Todos</label>
        </div>

        <!-- Botón de eliminar filtros con imagen -->
        <div class="filter-group-btn">
            <button type="button" id="resetFiltersBtn">
                <img src="../iconos/limpiar.png" alt="Eliminar Filtros">
            </button>
        </div>
    </div>

    <div class="card-body">
        <table id="crisisTable" class="table table-striped table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
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

<script src="../js/DTCrisis.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

</body>
</html>
