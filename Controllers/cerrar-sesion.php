<?php
session_start();
$_SESSION['usuario'] = '';
$_SESSION['nombre_usuario'] = '';
session_unset();
session_destroy();

header("Location: ../index.php");
exit();
?>
