<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); 
    exit();
}
$menu = 8;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .tabla-cat {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .tabla-cat th {
            background-color: #000; /* Fondo negro */
            color: #fff; /* Letras blancas */
            text-align: center; /* Texto centrado */
            padding: 10px;
        }

        .tabla-cat td {
            text-align: center; /* Texto centrado */
            padding: 10px;
        }

        .tabla-cat tr:nth-child(even) {
            background-color: #f2f2f2; /* Color de fila alternada */
        }

        .tabla-cat tr:hover {
            background-color: #ddd; /* Color al pasar el mouse */
        }

        .btn-agregar {
            margin-bottom: 15px;
        }

        .section-title {
            margin-top: 40px;
            font-weight: bold;
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
include("../Modals/modalNuevoCatCYC.php");
include("../Modals/modalEditarCatCYC.php");
include("../Modals/modalNuevoCanal.php");
include("../Modals/modalEditarCanal.php");
include("../Modals/modalNuevoBot.php");
include("../Modals/modalEditarBot.php");
include("../Modals/modalNuevoUbiIVR.php");
include("../Modals/modalEditarUbiIVR.php");
include("../Modals/modalNuevoProyecto.php");
include("../Modals/modalEditarProyecto.php");
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <img class="kenos-logo ms-auto" width="6%" src="../img/logo.png">
    </div>

    <div class="container mt-4">
        <h2>MENÚ DE CATÁLOGOS</h2>

        <!-- Tabla CyC's -->
        <h3 class="section-title">CyC's</h3>
        <button type="button" class=" btn-agregar" data-bs-toggle="modal" data-bs-target="#NuevoCatCoC">
            Agregar CyC
        </button>
        <table id="tabla_cycs_data" class="tabla-cat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Crisis</th>
                    <th>Criticidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas dinámicas -->
            </tbody>
        </table>

        <!-- Tabla Canales Digitales -->
        <h3 class="section-title">Canales Digitales</h3>
        <button type="button" class=" btn-agregar" data-bs-toggle="modal" data-bs-target="#NuevoCanal">
            Agregar Canal Digital
        </button>
        <table id="table-canales" class="tabla-cat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas dinámicas -->
            </tbody>
        </table>

        <!-- Tabla Bots -->
        <h3 class="section-title">Bots</h3>
        <button type="button" class=" btn-agregar" data-bs-toggle="modal" data-bs-target="#modalNuevoBot">
            Agregar Bot
        </button>
        <table id="table-bots" class="tabla-cat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas dinámicas -->
            </tbody>
        </table>

        <!-- Tabla Ubicaciones IVR -->
        <h3 class="section-title">Ubicaciones IVR</h3>
        <button type="button" class="btn-agregar" data-bs-toggle="modal" data-bs-target="#NuevaUbicacion">
            Agregar Ubicación IVR
        </button>
        <table id="table-ubicaciones" class="tabla-cat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas dinámicas -->
            </tbody>
            </table> 
        <h3 class="section-title">Proyectos</h3>
        <button type="button" class="btn-agregar" data-bs-toggle="modal" data-bs-target="#modalNuevoProyecto">
            Agregar Proyecto
        </button>    
        <table id="table-proyectos" class="tabla-cat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas dinámicas -->
            </tbody>
        
        </table>
    </div>
</div>

<script src="../Js/catalogos.js"></script>
<script src="../Js/DTCatCyC.js"></script>
<script src="../Js/DTCanales.js"></script>
<script src="../Js/DTBot.js"></script>
<script src="../Js/DTProyectos.js"></script>
<script src="../Js/DTUbicaciones.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    window.onload = function() {
        document.getElementById('splash').style.display = 'none';
    };

    // Mostrar tabla según selección en combo
    document.getElementById('combo-tablas').addEventListener('change', function() {
        const tablas = document.querySelectorAll('.tabla-container');
        tablas.forEach(tabla => tabla.style.display = 'none');
        const seleccion = this.value;
        document.getElementById(seleccion).style.display = 'block';
    });
</script>

</body>
</html>
