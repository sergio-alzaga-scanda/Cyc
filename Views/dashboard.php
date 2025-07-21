<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
}
$menu = 1;
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
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css">
    <style>
        .filtros-container { display: flex; gap: 10px; margin-bottom: 20px; }
        .filtros-container input, .filtros-container select, .filtros-container button {
            padding: 8px; font-size: 14px;
        }
    </style>
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
    <div class="filters-container" flex>

        <div class="filter-group">
            <label for="startDate" class="form-label">Fecha de Inicio:</label>
            <input type="date" id="startDate" class="form-control">
        </div>
        <div class="filter-group">
            <label for="startHour" class="form-label">Hora de Inicio:</label>
            <input type="time" id="startHour" class="form-control" type="time" id="startHour" value="08:00" step="3600" min="08:00" max="20:00" 
                   pattern="([0-9]{2}):([0-9]{2})" onchange="this.value = this.value.slice(0, 2) + ':00'" >
        </div>

        <div class="filter-group">
            <label for="endDate" class="form-label">Fecha de Fin:</label>
            <input type="date" id="endDate" class="form-control">
        </div>
        <div class="filter-group">
            <label for="endHour" class="form-label">Hora de Fin:</label>
            <input type="time" id="endHour" class="form-control" type="time" id="startHour" value="20:00" step="3600" min="08:00" max="20:00" 
                   pattern="([0-9]{2}):([0-9]{2})" onchange="this.value = this.value.slice(0, 2) + ':00'" >
        </div>

        <div class="filter-group">
            <label for="tipo" class="form-label">Tipo de Actividad</label>
            <select id="tipo" class="form-control">
                <option value="">Ambos</option>
                <option value="1">Contingencia</option>
                <option value="2">Crisis</option>
            </select>
        </div>

        <div class="filter-group">
            <button id="aplicarFiltros" title="Aplicar los filtros seleccionados" >
                <icon style="color: green; font-size: 22px" class="mdi mdi-filter" ></icon>
            </button>
        </div>

        <div >
            <button id="resetFiltros" title="Reestablecer Filtros">
                <tooltip></tooltip>
                <img src="../iconos/limpiar.png">
            </button>
        </div>
              
    </div>

        <!-- Gráfica -->
        <div class="card mb-4">
            <div class="card-body">
                <canvas id="actividadChart" width="400" height="100"></canvas>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <canvas id="actividadChartCategorias" width="400" height="200"></canvas>
            </div>
        </div>
              
    </div>

    

</div>

<!-- Scripts de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script src="../Js/DTDashboard.js"></script>


<script>
    window.onload = function() {
        document.getElementById('splash').style.display = 'none';
    };
</script>

</body>
       