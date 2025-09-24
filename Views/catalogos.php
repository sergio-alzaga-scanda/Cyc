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
    <title>Catalogos</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="shortcut icon" href="../img/logo.png">
    <link rel="icon" href="../img/logo.png" >

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
        /* Personalización de la cabecera del acordeón */
.accordion-button {
    background-color: #000 !important;  /* Fondo negro */
    color: #fff !important;  /* Texto blanco */
    border: 1px solid #000;  /* Bordes negros */
}

/* Cambiar color al hacer hover */
.accordion-button:not(.collapsed) {
    background-color: #333 !important;  /* Fondo gris oscuro cuando está expandido */
    color: #fff !important;  /* Mantener texto blanco */
}

/* Opcional: cambiar color de la cabecera del acordeón al estar colapsado */
.accordion-button.collapsed {
    background-color: #000 !important;  /* Fondo negro cuando está colapsado */
    color: #fff !important;  /* Texto blanco */
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

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <img class="kenos-logo ms-auto" width="6%" src="../img/logo.png">
    </div>

    <div class="container mt-4">
        <h2>MENÚ DE CATÁLOGOS</h2>

        <!-- CyC's -->
        <div class="accordion" id="accordionCyC">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingCyC">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCyC" aria-expanded="true" aria-controls="collapseCyC">
                      <b>  CyC's </b>
                    </button>
                </h2>
                <div id="collapseCyC" class="accordion-collapse collapse show" aria-labelledby="headingCyC" data-bs-parent="#accordionCyC">
                    <div class="accordion-body">
                        <button type="button" class=" btn-agregar" data-bs-toggle="modal" data-bs-target="#NuevoCatCoC">
                            Agregar &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="icono-plus">+</span>
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
                    </div>
                </div>
            </div>
        </div>
        <?php
            if($_SESSION['perfil'] === 1) {
                // Mostrar las secciones solo si el perfil no es 3

        ?>
        <!-- Canales Digitales -->
        <div class="accordion" id="accordionCanales">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingCanales">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCanales" aria-expanded="true" aria-controls="collapseCanales">
                       <b> Canales Digitales </b>
                    </button>
                </h2>
                <div id="collapseCanales" class="accordion-collapse collapse " aria-labelledby="headingCanales" data-bs-parent="#accordionCanales">
                    <div class="accordion-body">
                        <button type="button" class=" btn-agregar" data-bs-toggle="modal" data-bs-target="#NuevoCanal">
                            Agregar &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="icono-plus">+</span>
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Bots -->
        <div class="accordion" id="accordionBots">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingBots">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBots" aria-expanded="true" aria-controls="collapseBots">
                       <b> Bots </b>
                    </button>
                </h2>
                <div id="collapseBots" class="accordion-collapse collapse " aria-labelledby="headingBots" data-bs-parent="#accordionBots">
                    <div class="accordion-body">
                        <button type="button" class=" btn-agregar" data-bs-toggle="modal" data-bs-target="#modalNuevoBot">
                            Agregar &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="icono-plus">+</span>
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Ubicaciones IVR -->
        <div class="accordion" id="accordionUbicaciones">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingUbicaciones">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUbicaciones" aria-expanded="true" aria-controls="collapseUbicaciones">
                       <b> Ubicaciones IVR </b>
                    </button>
                </h2>
                <div id="collapseUbicaciones" class="accordion-collapse collapse " aria-labelledby="headingUbicaciones" data-bs-parent="#accordionUbicaciones">
                    <div class="accordion-body">
                        <button type="button" class="btn-agregar" data-bs-toggle="modal" data-bs-target="#NuevaUbicacion">
                            Agregar &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="icono-plus">+</span>
                        </button>
                        <table id="table-ubicaciones" class="tabla-cat">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Proyecto</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proyectos -->
        <div class="accordion" id="accordionProyectos">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingProyectos">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProyectos" aria-expanded="true" aria-controls="collapseProyectos">
                       <b> Proyectos </b>
                    </button>
                </h2>
                <div id="collapseProyectos" class="accordion-collapse collapse " aria-labelledby="headingProyectos" data-bs-parent="#accordionProyectos">
                    <div class="accordion-body">
                        <button type="button" class="btn-agregar" data-bs-toggle="modal" data-bs-target="#modalNuevoProyecto">
                            Agregar &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="icono-plus">+</span>
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
            </div>
        </div>
        <?php
            } // Cierre del if para perfil  
            ?>

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
</script>

</body>
</html>
