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
        <form action="../controllers/usuarios.php" method="POST" id="form-usuario">
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

          <div class="modal-footer">
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
