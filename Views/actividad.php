<?php
session_start();
if ($_SESSION['usuario'] < 0) {
    header("Location: ../index.php");
}
$menu = 7;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php 
include ("menu.php");
?>
    <div class="content">
        <div class="header">
            <button class="boton-formateado"  style="width: 200px; background-color:#4B4A4B ;">
                <b><span class="texto-formateado">Agregar</span></b>
                <img src="../iconos/add.png" width="13%">
            </button>
            <img class="kenos-logo" width="6%" src="../img/logo.png">
            
        </div>
       