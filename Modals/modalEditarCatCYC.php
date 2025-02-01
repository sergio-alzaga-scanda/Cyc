<!-- Modal -->
<div class="modal fade" id="modalEditarCatCYC" tabindex="-1" role="dialog" aria-labelledby="modalEditarCatCYCLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarCatCYCLabel">Editar categoría CoC</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario -->
        <form action="../Controllers/catCyC.php" method="POST" id="form-usuario">
          <input type="text" name="accion" id="accion" hidden value="3"> <!-- Acción de edición -->

          <div class="mb-3">
            <input type="text" name="id" id="edit_id" hidden> <!-- ID de la crisis -->
            <input type="text" name="nombre" id="edit_nombre" required class="form-control form-input" placeholder="Nombre de la CoC" aria-label="Nombre de la CoC">
          </div>
          
          <div class="mb-3">
            <select class="form-select form-input" required name="criticidad" id="edit_criticidad">
              <option selected disabled class="d-none">Criticidad</option>
              <option value="Baja">Baja</option>
              <option value="Media">Media</option>
              <option value="Alta">Alta</option>
            </select>
          </div>

          <div hidden class="mb-3">
            <select class="form-select form-input" name="status" id="edit_status">
              <option value="1">Activo</option>
              <option value="2">Inactivo</option>
            </select>
          </div>

          <!-- Modal Footer -->
          <div class="modal-footer d-flex justify-content-center">
    <div class="btn-container">
        <!-- Botón Guardar y habilitar -->
        <button type="submit" class="btn-icon">
            <span>Guardar y habilitar</span>
            <img src="../iconos/guardar.png" alt="Guardar">
        </button>

        <!-- Botón Cancelar -->
        <button type="button" class="btn-icon" data-bs-dismiss="modal">
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
