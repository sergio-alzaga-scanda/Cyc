<!-- Modal: Editar Ubicación IVR -->
<div class="modal fade" id="modalEditarUbicacionIVR" tabindex="-1" aria-labelledby="modalEditarUbicacionIVRLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="../Controllers/catUbicaciones.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarUbicacionIVRLabel">Editar Ubicación IVR</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="accion" id="accion_editar" value="3">
          <input type="hidden" name="edit_id_ubicacion_ivr" id="edit_id_ubicacion_ivr">

          <div class="mb-3">
            <label for="edit_nombre_ubicacion_ivr" class="form-label">Nombre de la Ubicación:</label>
            <input type="text" class="form-control" id="edit_nombre_ubicacion_ivr" name="nombre" required>
          </div>

          <div class="mb-3">
            <label for="edit_proyecto_select" class="form-label">Proyecto:</label>
            <select class="form-select" id="edit_proyecto_select" name="proyecto" required>
              <option value="">Seleccione un proyecto</option>
              <?php
              include("../Controllers/bd.php");
              $q = "SELECT id_proyecto, nombre_proyecto FROM proyectos WHERE status = 1 ORDER BY nombre_proyecto";
              $res = $conn->query($q);
              while ($rw = $res->fetch_assoc()) {
                  echo '<option value="' . $rw['id_proyecto'] . '">' . $rw['nombre_proyecto'] . '</option>';
              }
              ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

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

  .modal-body {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .form-control, .form-select {
    width: 80%;
    margin: 10px 0;
  }

  .form-input {
    border: none;
    border-bottom: 2px solid #ccc;
    border-radius: 0;
    outline: none;
    background-color: transparent;
    padding: 10px;
  }

  .form-input:focus {
    border-bottom: 2px solid #007bff;
  }
</style>
