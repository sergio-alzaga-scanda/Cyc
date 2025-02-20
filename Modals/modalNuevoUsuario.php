<?php
include("../Controllers/bd.php");

// Obtener los perfiles
$queryPerfil = "SELECT * FROM perfil ORDER BY nombre_perfil ASC";
$perfil_data = $conn->prepare($queryPerfil);

if ($perfil_data->execute()) {
    $perfiles = $perfil_data->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros
} else {
    echo "Error al obtener los perfiles";
}
?>

<!-- Modal -->
<div class="modal fade" id="NuevoUsuario" tabindex="-1" role="dialog" aria-labelledby="NuevoUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog " role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Nuevo Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario -->
        <form action="../Controllers/usuarios.php" method="POST" id="form-usuario">
          <input type="text" name="accion" id="accion" hidden value="1">

          <div class="mb-3">
            <input type="text" name="nombre_usuario" id="nombre_usuario" required class="form-control form-input" placeholder="Nombre de usuario" aria-label="Nombre de usuario">
          </div>
          <div class="mb-3">
            <input type="email" name="correo_usuario" id="correo_usuario" required class="form-control form-input" placeholder="Correo electrónico" aria-label="Correo electrónico">
          </div>
          <div class="mb-3">
            <input type="text" name="puesto_usuario" id="puesto_usuario" required class="form-control form-input" placeholder="Puesto" aria-label="Puesto">
          </div>
          <div class="mb-3">
            <input type="text" name="telefono_usuario" id="telefono_usuario" required class="form-control form-input" placeholder="Teléfono" aria-label="Teléfono">
          </div>
          <div class="mb-3">
            <select class="form-select form-input" required name="perfil_usuario" id="perfil_usuario">
              <option selected disabled class="d-none">Perfil de usuario</option>
              <?php
                foreach ($perfiles as $row) {
                    echo '<option value="' . $row['id_Perfil'] . '">' . $row['nombre_perfil'] . '</option>';
                }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <select class="form-select form-input" required name="status" id="status">
              <option value="1" selected>Activo</option>
              <option value="2">Inactivo</option>
            </select>
          </div>
          <!-- Este campo es solo para visualizar la fecha y hora de creación, no debe ser llenado manualmente -->
          <input type="text" name="fecha_creacion" id="fecha_creacion" hidden value="<?php echo date('Y-m-d H:i:s'); ?>" />

         <div class="modal-footer d-flex justify-content-center">
    <div class="btn-container">
        <!-- Botón Guardar y habilitar -->
        <button type="submit" class="btn-icon" style="border-radius: 15px;">
            <span>Guardar</span>
            <img src="../iconos/Group-4.svg" alt="Guardar">
        </button>

        <!-- Botón Cancelar -->
        <button type="button" class="btn-icon" style="border-radius: 15px;" data-bs-dismiss="modal">
            <span>Cancelar</span>
            <img src="../iconos/cancelar.png" alt="Cancelar">
        </button>
    </div>
</div>

        </form>
      </div>
    </div>
  </div>
</div>

<!-- Enlace al archivo JS -->


<!-- Estilos CSS adicionales -->
<style>

  .modal-footer {
    display: flex;
    justify-content: center; /* Centra el contenido horizontalmente */
    align-items: center; /* Asegura que todo esté alineado verticalmente */
}

.btn-container {
    display: flex;
    justify-content: center; /* Centra los botones horizontalmente */
    align-items: center; /* Centra los botones verticalmente */
    gap: 20px; /* Espacio entre los botones */
}

.btn-icon {
    display: flex;
    align-items: center;
    justify-content: center; /* Centra texto e imagen dentro del botón */
    padding: 10px 20px;
    gap: 10px; /* Espacio entre el texto y la imagen */
    background-color: #4B4A4B; /* Fondo del botón */
    border: none;
    color: white; /* Color del texto */
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease; /* Efecto suave al pasar el cursor */
}

.btn-icon:hover {
    background-color: #5A595A; /* Efecto hover */
}

.btn-icon img {
    height: 24px; /* Tamaño de la imagen */
    width: 24px;
}

  /* Centrando el formulario en la página */
  .modal-body {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  /* Ajustando el tamaño de los inputs para que estén alineados y centrados */
  .form-control, .form-select {
    width: 80%; /* Puedes ajustar el porcentaje del ancho según lo necesites */
    margin: 10px 0;
  }

  /* Estilos para los inputs: solo mostrar el borde inferior */
  .form-input {
    border: none;
    border-bottom: 2px solid #ccc;
    border-radius: 0;
    outline: none;
    background-color: transparent;
    padding: 10px;
  }

  /* Foco en el borde inferior */
  .form-input:focus {
    border-bottom: 2px solid #007bff; /* Cambia el color según tus preferencias */
  }
</style>
