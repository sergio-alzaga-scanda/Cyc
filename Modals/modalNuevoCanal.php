
<!-- Modal -->
<div class="modal fade" id="NuevoCanal" tabindex="-1" role="dialog" aria-labelledby="NuevoCanalLabel" aria-hidden="true">
  <div class="modal-dialog " role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Nuevo canal digital</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario -->
        <form action="../controllers/catCanales.php" method="POST" id="form-usuario">
          <input type="text" name="accion" id="accion" hidden value="1">

          <div class="mb-3">
            <input type="text" name="nombre" id="nombre" required class="form-control form-input" placeholder="Nombre del canal" aria-label="Nombre del canal">
          </div>
          
          
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
