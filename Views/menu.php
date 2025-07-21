<?php
include("../Controllers/bd.php");

// Después:
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$id_usuario     = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_usuario'];

$queryTbl = "SELECT * FROM permisos WHERE id_usuario = ?";
$stmt = $conn->prepare($queryTbl);
$stmt->bind_param("i", $id_usuario); // "i" para entero

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $permisos = $result->fetch_assoc(); // ✅ Forma correcta con mysqli

    if ($permisos) {
        $dashboard    = $permisos['dashboard'];
        $CyC          = $permisos['CyC'];
        $Usuarios     = $permisos['Usuarios'];
        $Perfiles     = $permisos['Perfiles'];
        $Avisos       = $permisos['Avisos'];
        $Actividad    = $permisos['Actividad'];
        $Catalogos    = $permisos['Catalogos'];
        $c_agregar    = $permisos['c_agregar'];
        $c_eliminar   = $permisos['c_eliminar'];
        $c_habilitar  = $permisos['c_habilitar'];
        $c_filtrar    = $permisos['c_filtrar'];
        $d_filtrar    = $permisos['d_filtrar'];
        $d_visualizar = $permisos['d_visualizar'];
        $h_detalle    = $permisos['h_detalle'];
        $h_filtrar    = $permisos['h_filtrar'];
        $u_agregar    = $permisos['u_agregar'];
        $u_eliminar   = $permisos['u_eliminar'];
        $u_modificar  = $permisos['u_modificar'];
        $u_habilitar  = $permisos['u_habilitar'];
        $u_filtrar    = $permisos['u_filtrar'];
        $p_agregar    = $permisos['p_agregar'];
        $p_eliminar   = $permisos['p_eliminar'];
        $p_modificar  = $permisos['p_modificar'];
        $p_filtrar    = $permisos['p_filtrar'];
        $cat_cyc      = $permisos['cat_cyc'];
        $cat_bot      = $permisos['cat_bot'];
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            width: 200px;
            background-color: #f4f4f4;
            transition: transform 0.3s ease, width 0.3s ease;
            transform: translateX(0);
            padding-top: 20px;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            z-index: 999;
        }

        .sidebar.active {
            transform: translateX(-250px);
        }

        #menu-toggle {
            background: none;
            border: none;
            font-size: 30px;
            cursor: pointer;
            padding: 10px;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1000;
            transition: background-color 0.3s ease;
        }

        #menu-toggle.active {
            background-color: #4F8978;
            color: white;
        }

        .content {
            margin-left: 170px;
            padding: 20px;
            flex: 1;
            transition: margin-left 0.3s ease;
        }

        .content.full-width {
            margin-left: 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .content {
                margin-left: 0;
            }
        }

        ul li {
            list-style: none;
            padding: 15px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        ul li.active, ul li:hover {
            background-color: #ddd;
        }

        a.menu-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            width: 100%;
        }
    </style>
</head>
<body>


<!-- Botón para activar el menú -->
<button id="menu-toggle" aria-label="Toggle menu">
    &#9776; <!-- Esto es el símbolo de la hamburguesa -->
</button>

<div class="sidebar">
    <div class="profile" style="text-align: center; padding-left: 20%;">
        <a style="text-decoration: none; color: inherit;" href="perfil_usuario.php">
            <img src="../iconos/Vector-1.svg">
            <br>
            <span><?php echo $nombre_usuario; ?></span>
            <br>
            <span style="font-size: 14px;">Mi perfil</span>
        </a>
    </div>
    <b>
    <br>
    <ul>
       
            <a href="dashboard.php" style="text-decoration: none; color: inherit;">
                <li <?php if ($menu === 1) { echo 'class="active"'; } ?> >
                    <img src="../iconos/Vector-2.svg"> &nbsp;&nbsp;
                    Dashboard
                </li>
            </a>
      
       

          
                <a href="cyc.php" style="text-decoration: none; color: inherit;">
                    <li <?php if ($menu === 2) { echo 'class="active"'; } ?> >
                        <img src="../iconos/Vector-3.svg"> &nbsp;&nbsp;
                         CyC's
                     </li>
                </a>
           

           
                <a href="historico.php" style="text-decoration: none; color: inherit;">
                    <li <?php if ($menu === 3) { echo 'class="active"'; } ?> >
                        <img src="../iconos/entypo_archive.svg"> &nbsp;&nbsp;
                         Histórico
                    </li>
                </a>
           

           
                <a href="usuarios.php" style="text-decoration: none; color: inherit;">
                    <li <?php if ($menu === 4) { echo 'class="active"'; } ?> >
                        <img src="../iconos/Vector-4.svg"> &nbsp;&nbsp;
                         Usuarios
                    </li>
                </a>
           

         
                <a href="perfiles.php" style="text-decoration: none; color: inherit;">
                    <li <?php if ($menu === 5) { echo 'class="active"'; } ?> >
                        <img src="../iconos/Vector-5.svg"> &nbsp;&nbsp;
         				Perfiles
                    </li>
                </a>
            

           
                <a href="avisos.php" style="text-decoration: none; color: inherit;">
                    <li <?php if ($menu === 6) { echo 'class="active"'; } ?> >
                       <img src="../iconos/Group.svg"> &nbsp;&nbsp;
                       Avisos
                    </li>
                </a>
           

            
                <a href="actividad.php" style="text-decoration: none; color: inherit;">
                    <li <?php if ($menu === 7) { echo 'class="active"'; } ?> >
                        <img src="../iconos/Group 7.svg"> &nbsp;&nbsp;
                         <b>Actividad</b>
                    </li>
                </a>
           

            
                <a href="catalogos.php" style="text-decoration: none; color: inherit;">
                    <li <?php if ($menu === 8) { echo 'class="active"'; } ?> >
                        <img src="../iconos/catalogos.png"> &nbsp;&nbsp;
                    <b>Catálogos</b>
                    </li>
                </a>
            

            <a href="../Controllers/cerrar-sesion.php" style="text-decoration: none; color: inherit;">
                <li>
                    <img src="../iconos/Vector-6.svg"> &nbsp;&nbsp;
                    Cerrar sesión
                </li>
            </a>
        </ul>

    </b>
</div>

<div class="content" id="main-content">
    <?php // El contenido se mostrará dinámicamente aquí, NO QUITAR POR FAVOR !!!!! ?>
</div>

<script>
    const sidebar      = document.querySelector('.sidebar');
    const content      = document.getElementById('main-content');
    const toggleButton = document.getElementById('menu-toggle');
    const menuLinks    = document.querySelectorAll('li[data-url]');

    function toggleSidebar() {
        const isActive = sidebar.classList.toggle('active');
        toggleButton.classList.toggle('active');
        content.classList.toggle('full-width', isActive);
    }

    toggleButton.addEventListener('click', toggleSidebar);

    function adjustLayout() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
            toggleButton.classList.remove('active');
            content.classList.remove('full-width');
        } else {
            sidebar.classList.add('active');
            toggleButton.classList.add('active');
            content.classList.add('full-width');
        }
    }

    window.addEventListener('resize', adjustLayout);
    window.addEventListener('load', adjustLayout);

    // Cargar contenido dinámico
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            const url = link.getAttribute('data-url');
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    content.innerHTML = html;
                    adjustLayout();
                })
                .catch(err => console.error('Error al cargar el contenido:', err));
        });
    });
</script>




</body>
</html>