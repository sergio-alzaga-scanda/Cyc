<?php
include("../Controllers/bd.php");
$id_usuario = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_usuario'];
$queryTbl = "SELECT * FROM permisos WHERE id_usuario = :id_usuario";
$stmt = $conn->prepare($queryTbl);
$stmt->bindParam(':id_usuario', $id_usuario);

if ($stmt->execute()) {
    $permisos = $stmt->fetch(PDO::FETCH_ASSOC);
    // Now access each permission directly without foreach
    $id_usuario = $permisos['id_usuario'];
    $dashboard = $permisos['dashboard'];
    $CyC = $permisos['CyC'];
    $Usuarios = $permisos['Usuarios'];
    $Perfiles = $permisos['Perfiles'];
    $Avisos = $permisos['Avisos'];
    $Actividad = $permisos['Actividad'];
    $Catalogos = $permisos['Catalogos'];
    $c_agregar = $permisos['c_agregar'];
    $c_eliminar = $permisos['c_eliminar'];
    $c_habilitar = $permisos['c_habilitar'];
    $c_filtrar = $permisos['c_filtrar'];
    $d_filtrar = $permisos['d_filtrar'];
    $d_visualizar = $permisos['d_visualizar'];
    $h_detalle = $permisos['h_detalle'];
    $h_filtrar = $permisos['h_filtrar'];
    $u_agregar = $permisos['u_agregar'];
    $u_eliminar = $permisos['u_eliminar'];
    $u_modificar = $permisos['u_modificar'];
    $u_habilitar = $permisos['u_habilitar'];
    $u_filtrar = $permisos['u_filtrar'];
    $p_agregar = $permisos['p_agregar'];
    $p_eliminar = $permisos['p_eliminar'];
    $p_modificar = $permisos['p_modificar'];
    $p_filtrar = $permisos['p_filtrar'];
    $cat_cyc = $permisos['cat_cyc'];
    $cat_bot = $permisos['cat_bot'];

}
?>
    <div class="sidebar">
        <div class="profile" style="text-align: center; padding-left: 20%;">
            <a style="text-decoration: none; color:inherit;" href=perfil_usuario.php>
                <img src="../iconos/Vector-1.svg">
                <br>
                <span><?php echo $nombre_usuario; ?></span>
                <br>
                <span style="font-size: 14px;">Mi perfil</span>
            </a>
            
        </div>
        <a href=""></a>
    <b>
        <ul>
    <?php if ($dashboard == 1) { ?>
        <a href="inicio.php" style="text-decoration: none; color: inherit;">
            <li <?php if ($menu === 1) { echo 'class="active"'; } ?> >
                <img src="../iconos/Vector-2.svg"> &nbsp;&nbsp;
                Dashboard
            </li>
        </a>
    <?php } ?>

    <?php if ($CyC == 1) { ?>
        <a href="cyc.php" style="text-decoration: none; color: inherit;">
            <li <?php if ($menu === 2) { echo 'class="active"'; } ?> >
                <img src="../iconos/Vector-3.svg"> &nbsp;&nbsp;
                 CyC's
             </li>
        </a>
    <?php } ?>

    <?php if ($Actividad == 1) { ?>
        <a href="historico.php" style="text-decoration: none; color: inherit;">
            <li <?php if ($menu === 3) { echo 'class="active"'; } ?> >
                <img src="../iconos/entypo_archive.svg"> &nbsp;&nbsp;
                 Histórico
            </li>
        </a>
    <?php } ?>

    <?php if ($Usuarios == 1) { ?>
        <a href="usuarios.php" style="text-decoration: none; color: inherit;">
            <li <?php if ($menu === 4) { echo 'class="active"'; } ?> >
                <img src="../iconos/Vector-4.svg"> &nbsp;&nbsp;
                 Usuarios
            </li>
        </a>
    <?php } ?>

    <?php if ($Perfiles == 1) { ?>
        <a href="perfiles.php" style="text-decoration: none; color: inherit;">
            <li <?php if ($menu === 5) { echo 'class="active"'; } ?> >
                <img src="../iconos/Vector-5.svg"> &nbsp;&nbsp;
 				Perfiles
            </li>
        </a>
    <?php } ?>

    <?php if ($Avisos == 1) { ?>
        <a href="avisos.php" style="text-decoration: none; color: inherit;">
            <li <?php if ($menu === 6) { echo 'class="active"'; } ?> >
               <img src="../iconos/Group.svg"> &nbsp;&nbsp;
               Avisos
            </li>
        </a>
    <?php } ?>

    <?php if ($Actividad == 1) { ?>
        <a href="actividad.php" style="text-decoration: none; color: inherit;">
            <li <?php if ($menu === 7) { echo 'class="active"'; } ?> >
                <img src="../iconos/Group 7.svg"> &nbsp;&nbsp;
                 Actividad
            </li>
        </a>
    <?php } ?>

    <?php if ($Catalogos) { ?>
        <a href="catalogos.php" style="text-decoration: none; color: inherit;">
            <li <?php if ($menu === 8) { echo 'class="active"'; } ?> >
                <img src="../iconos/catalogos.png"> &nbsp;&nbsp;
            Catálogos
            </li>
        </a>
    <?php } ?>

    <a href="../Controllers/cerrar-sesion.php" style="text-decoration: none; color: inherit;">
        <li>
            <img src="../iconos/Vector-6.svg"> &nbsp;&nbsp;
            Cerrar sesión
        </li>
    </a>
</ul>

    </b>
    </div>