<!-- Modal -->
<div class="modal fade" id="modalEditUsuarios" tabindex="-1" role="dialog" aria-labelledby="modalEditUsuariosLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Editar Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario -->
        <form action="../controllers/usuarios.php" method="POST" id="form-usuario">
          <input type="text" name="accion" id="accion" hidden value="3"> <!-- Cambiar acción a 'editar' -->
          <input type="text" name="id_usuario" id="edit_id_usuario" hidden> <!-- Campo para el ID del usuario cuando se edita -->

          <div class="mb-3">
            <input type="text" name="nombre_usuario" id="edit_nombre_usuario" required class="form-control form-input" placeholder="Nombre de usuario" aria-label="Nombre de usuario">
          </div>
          <div class="mb-3">
            <input type="email" name="correo_usuario" id="edit_correo_usuario" required class="form-control form-input" placeholder="Correo electrónico" aria-label="Correo electrónico">
          </div>
          <div class="mb-3">
            <input type="text" name="puesto_usuario" id="edit_puesto_usuario" required class="form-control form-input" placeholder="Puesto" aria-label="Puesto">
          </div>
          <div class="mb-3">
            <input type="text" name="telefono_usuario" id="edit_telefono_usuario" required class="form-control form-input" placeholder="Teléfono" aria-label="Teléfono">
          </div>
          <div class="mb-3">
    <select class="form-select form-input" required name="perfil_usuario" id="edit_perfil_usuario">
        <option selected disabled class="d-none">Perfil de usuario</option>
        <?php
            // Asegúrate de que los perfiles estén siendo recuperados y mostrados correctamente
            foreach ($perfiles as $row) {
                echo '<option value="' . $row['id_Perfil'] . '">' . $row['nombre_perfil'] . '</option>';
            }
        ?>
    </select>
</div>
          <div class="mb-3">
            <select class="form-select form-input" required name="status" id="edit_status">
              <option value="1">Activo</option>
              <option value="2">Inactivo</option>
            </select>
          </div>
          <!-- Campo para la contraseña -->
         <! <div class="mb-3">
    <input type="password" name="password_usuario" hidden id="edit_password_usuario" class="form-control form-input" placeholder="Contraseña" aria-label="Contraseña">
    <button type="button" id="togglePassword" class="btn btn-light mt-2" hidden style="width: 100%; border: none; background: transparent;">
        <i id="toggleIcon" class="bi bi-eye"></i> Ver Contraseña
    </button>
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

<!-- Añadir el icono de "ojo" para la visualización -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
