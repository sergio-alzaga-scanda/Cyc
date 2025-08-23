<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
}
$menu = 7;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda de Capacitaciones</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo.png">
    <link rel="icon" href="../img/logo.png">

    <!-- CSS y JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style_tablas.css">

    <style>
        #agendaTable th, #agendaTable td {
            white-space: nowrap;
        }
    </style>
</head>
<body>

<!-- Splash -->
<div id="splash" class="splash-container">
    <div class="spinner-border" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<?php include("menu.php"); ?>

<div class="container mt-4">
    <div class="d-flex justify-content-end align-items-center mb-4">
        <img class="kenos-logo" width="6%" src="../img/logo.png">
    </div>

    <!-- Filtros -->
    <div class="filters-container mb-3 d-flex flex-wrap gap-3">
        <div>
            <label for="searchText" class="form-label">Buscar</label>
            <input type="text" id="searchText" size="45" class="form-control" placeholder="Buscar...">
        </div>
        <div>
            <label for="startDate" class="form-label">Fecha de salida</label>
            <input type="date" id="startDate" class="form-control">
        </div>
        <div>
            <label for="endDate" class="form-label">Fecha de regreso</label>
            <input type="date" id="endDate" class="form-control">
        </div>
        <div>
            <label for="monthSelect" class="form-label">Filtrar por mes</label>
            <select id="monthSelect" class="form-select">
                <option value="">Todos</option>
                <option value="08">Agosto</option>
                <option value="09">Septiembre</option>
            </select>
        </div>
        <div class="align-self-end">
            <button type="button" id="resetFiltersBtn" class="btn btn-secondary">
                <img src="../iconos/limpiar.png" alt="Limpiar" style="width: 20px;"> Limpiar
            </button>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card-body">
        <table id="agendaTable" class="table-custom table table-striped table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th># Empleado</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Proyecto</th>
                    <th>Empresa</th>
                    <th>Destino</th>
                    <th>Fecha Salida</th>
                    <th>Fecha Regreso</th>
                    <th>Itinerario</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>01855</td>
                    <td>Sergio Armando Alzaga Díaz</td>
                    <td>sergio.alzaga@scanda.com.mx</td>
                    <td>SwiftDesk</td>
                    <td>Kenos</td>
                    <td>SAP Puebla</td>
                    <td>30-ago-2025</td>
                    <td>01-sep-2025</td>
                    <td><a href="#" class="btn btn-sm btn-primary">Ver Itinerario</a></td>
                </tr>
                <tr>
                    <td>01847</td>
                    <td>María Fernanda Reyes</td>
                    <td>fernanda.reyes@scanda.com.mx</td>
                    <td>SwiftDesk</td>
                    <td>Kenos</td>
                    <td>SAP Puebla</td>
                    <td>28-ago-2025</td>
                    <td>31-ago-2025</td>
                    <td><a href="#" class="btn btn-sm btn-primary">Ver Itinerario</a></td>
                </tr>
                <tr>
                    <td>01853</td>
                    <td>Juan Carlos Domínguez</td>
                    <td>juan.dominguez@scanda.com.mx</td>
                    <td>SwiftDesk</td>
                    <td>Kenos</td>
                    <td>SAP Puebla</td>
                    <td>29-ago-2025</td>
                    <td>02-sep-2025</td>
                    <td><a href="#" class="btn btn-sm btn-primary">Ver Itinerario</a></td>
                </tr>
                <tr>
                    <td>01849</td>
                    <td>Daniela López Pérez</td>
                    <td>daniela.lopez@scanda.com.mx</td>
                    <td>SwiftDesk</td>
                    <td>Kenos</td>
                    <td>SAP Puebla</td>
                    <td>30-ago-2025</td>
                    <td>01-sep-2025</td>
                    <td><a href="#" class="btn btn-sm btn-primary">Ver Itinerario</a></td>
                </tr>
                <tr>
                    <td>01868</td>
                    <td>Ricardo Hernández Ruiz</td>
                    <td>ricardo.hernandez@scanda.com.mx</td>
                    <td>SwiftDesk</td>
                    <td>Kenos</td>
                    <td>SAP Puebla</td>
                    <td>27-ago-2025</td>
                    <td>01-sep-2025</td>
                    <td><a href="#" class="btn btn-sm btn-primary">Ver Itinerario</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- JS de Bootstrap y DataTables -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    window.onload = function () {
        document.getElementById('splash').style.display = 'none';
    };

    $(document).ready(function () {
        const table = $('#agendaTable').DataTable({
    responsive: true,
    dom: 'rtip', // oculta barra de búsqueda nativa
    language: {
        url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
    }
});

        // Filtro de búsqueda general
        $('#searchText').on('keyup', function () {
            table.search(this.value).draw();
        });

        // Filtro por fecha
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            let min = $('#startDate').val();
            let max = $('#endDate').val();
            let fechaSalida = data[6];

            if (!min && !max) return true;

            const parseDate = str => new Date(str.split("-").reverse().join("-"));
            let salida = parseDate(fechaSalida);

            if ((min === "" || salida >= new Date(min)) &&
                (max === "" || salida <= new Date(max))) {
                return true;
            }
            return false;
        });

        $('#startDate, #endDate').change(function () {
            table.draw();
        });

        // Filtro por mes
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            const selectedMonth = $('#monthSelect').val();
            const fechaSalida = data[6];

            if (!selectedMonth) return true;

            const monthMap = {
                'ene': '01', 'feb': '02', 'mar': '03', 'abr': '04',
                'may': '05', 'jun': '06', 'jul': '07', 'ago': '08',
                'sep': '09', 'oct': '10', 'nov': '11', 'dic': '12'
            };

            const monthStr = fechaSalida.split('-')[1].toLowerCase().substring(0, 3);
            const rowMonth = monthMap[monthStr];

            return rowMonth === selectedMonth;
        });

        $('#monthSelect').on('change', function () {
            table.draw();
        });

        // Reset filtros
        $('#resetFiltersBtn').click(function () {
            $('#searchText').val('');
            $('#startDate').val('');
            $('#endDate').val('');
            $('#monthSelect').val('');
            table.search('').draw();
            table.draw();
        });
    });
</script>

</body>
</html>
