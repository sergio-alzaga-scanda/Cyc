<?php
// session_start();
// if (!$_SESSION['usuario']) {
//     header("Location: ../index.php"); 
// }
$menu = 7;
?>
<!DOCTYPE html>
<html lang="en">
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
    <link rel="shortcut icon" href="../img/logo.png">
    <link rel="icon" href="../img/logo.png" >
    <title>Actividad</title>
</head>

    <style>
        #actividadTable th, #actividadTable td {
            width: 25%; /* Ajusta el porcentaje según lo necesites */
            white-space: nowrap; /* Evita el salto de línea */
        }

        #actividadTable th:nth-child(4), 
        #actividadTable td:nth-child(4) {
            width: 40%; /* Columna 'Descripción' más ancha */
        }
    </style>


</head>
<body>

   <!-- Splash de carga -->
<div id="splash" class="splash-container">
    <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<?php 
include("menu.php");
?>

<div class="container mt-4">
    <div class="d-flex justify-content-end align-items-center mb-4">
        <img class="kenos-logo" width="6%" src="../img/logo.png">
    </div>

    <!-- Filtros en columna (vertical) -->
    <div class="filters-container mb-3">
        <div style="padding-right: 5%;" >
            <label for="searchText" class="form-label">Buscar</label>
            <input type="text" id="searchText" size="45" class="form-control" placeholder="Buscar...">
        </div>

        <div class="filter-group">
            <label for="startDate" class="form-label">Filtrar por fecha de inicio</label>
            <input type="date" id="startDate" class="form-control">
        </div>
        <div class="filter-group">
            <label for="endDate" class="form-label">Filtrar por fecha de fin</label>
            <input type="date" id="endDate" class="form-control">
        </div>

         <!-- Botón de eliminar filtros con imagen -->
        <div class="filter-group-btn" style="margin-top: 1.5em;">
            <button type="button" id="resetFiltersBtn">
                <img src="../iconos/limpiar.png" alt="Eliminar Filtros">
            </button>
        </div>
              
    </div>

    <div class="card-body">
        <table id="actividadTable" class="table-custom table table-striped table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>ID Usuario</th>
                    <th>Usuario</th>
                    <th>Descripciòn</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se llenarán las filas con datos mediante AJAX -->
            </tbody>
        </table>
    </div>

</div>

<!-- Scripts de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script src="../Js/DTActividad.js"></script>


<script>
    window.onload = function() {
        document.getElementById('splash').style.display = 'none';
    };
</script>

</body>
       