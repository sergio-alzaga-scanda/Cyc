<!-- Splash de carga -->
<div id="splash" class="splash">
  <div class="loader"></div>
  <p>Cargando...</p>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Editar Crisis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="../Controllers/crisis.php" method="POST" id="form-cyc">
          <input type="text" name="accion" id="accion" hidden value="5">
          <input type="text" name="id" id="id" hidden>

          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <input type="text" name="no_ticket" readonly id="no_ticket_edit" required class="form-control" placeholder="Numero de ticket" aria-label="Numero de ticket">
            </div>
            <div class="col-md-9">
              <input type="text" name="nombre" id="nombre_edit" required class="form-control" placeholder="Nombre" aria-label="Nombre">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <input class="form-check-input" checked name="programar" id="programar_edit" type="checkbox" id="programar">
              <label class="form-check-label" style="padding-top: 2px;" for="programar">Programar CoC</label>
            </div>
            <div class="col-md-3" id="fecha-bloque-edit">
              <div class="input-group">
                <input type="datetime-local" name="fecha_programacion_2" class="form-control" id="fecha_programacion_2" aria-describedby="fecha-icono">
              </div>
            </div>
          </div>

         <!-- Sección 1 -->
<div class="mb-4">
  <div class="row g-3 mb-3">
    <div class="col-md-2">
      <select class="form-select" name="categoria_edit" required id="categoria_edit">
        <option selected disabled class="d-none">Categoría</option>
        <?php
          foreach ($crisis as $row) {
              echo '<option value="' . $row['id'] . '" data-criticidad="' . $row['criticidad'] . '">' . $row['nombre_crisis'] . '</option>';
          }
        ?>
      </select>
    </div>
    <div class="col-md-2">
      <label for="criticidad" id="criticidad-label-edit" style="text-align: center;" class="form-label"></label>
    </div>
    <div class="col-md-2">
      <select class="form-select" required name="tipo_edit" id="tipo_edit">
        <option selected disabled class="d-none">Tipo</option>
        <option value="1">Contingencia</option>
        <option value="2">Crisis</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" required name="ubicacion_edit" id="ubicacion_edit">
        <option selected disabled class="d-none">Ubicación</option>
        <?php foreach ($ubicaciones as $row) : ?>
          <option value="<?php echo $row['id_ubicacion_ivr']; ?>"><?php echo $row['nombre_ubicacion_ivr']; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" required name="edit_proyecto" id="edit_proyecto">
        <option selected disabled class="d-none">Proyecto</option>
        <?php
          foreach ($proyectos as $row_proyecto) {
              echo '<option value="' . $row_proyecto['id_proyecto'] . '">' . $row_proyecto['nombre_proyecto'] . '</option>';
          }
        ?>
      </select>
    </div>
  </div>
  <div class="mt-3">
    <label for="ivr" class="form-label fw-bold">Redacción para grabación en IVR</label>
    <textarea required class="form-control" name="ivr_edit" id="ivr_edit" rows="3"></textarea>
  </div>
</div>

          <!-- Sección 2 -->
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="habilitar-canal-digital-edit" id="habilitar-canal-digital-edit">
            <label class="form-check-label" for="habilitar-canal-digital-edit">Habilitar canal digital</label>
          </div>

          <div id="contenido-canal-digital-edit" style="display: none;">
            <div class="row g-3">
              <div class="col-md-4">
                <select class="form-select selectpicker" disabled id="canal_edit" name="canal_edit[]" multiple data-live-search="true">
                  <option selected disabled class="d-none">Canales digitales</option>
                  <?php
                    foreach ($canales as $row) {
                        echo '<option value="' . $row['id_canal_digital'] . '">' . $row['nombre_canal'] . '</option>';
                    }
                  ?>
                </select>
              </div>
              <div class="col-md-4">
                <select class="form-select selectpicker" disabled id="bot_edit" name="bot_edit[]" multiple data-live-search="true">
                  <option selected disabled class="d-none">Bots</option>
                  <?php
                    foreach ($bots as $row) {
                        echo '<option value="' . $row['id_bot'] . '">' . $row['nombre_bot'] . '</option>';
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="form-check mt-3">
              <input class="form-check-input"  type="checkbox" name="mismo-canal-edit" id="mismo-canal-edit">
              <label class="form-check-label" for="mismo-canal-edit">La redacción para el canal es la misma que la redacción del IVR</label>
            </div>
            <div class="mt-3">
              <label for="canal-digital-texto-edit" class="form-label fw-bold">Redacción para grabación en canal digital</label>
              <textarea class="form-control" name="redaccion_canales_edit" id="redaccion_canales_edit" rows="3"></textarea>
            </div>
          </div>

          <!-- Botones -->
      </div>
      <div class="modal-footer d-flex justify-content-center">
    <div class="btn-container">
        <!-- Botón Guardar y habilitar -->
        <button type="submit" class="btn-icon" style="border-radius: 15px;">
            <span>Guardar y habilitar</span>
            <img src="../iconos/guardar.png" alt="Guardar">
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

<style type="text/css">
  /* Estilo para el splash */
  .splash {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.7);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    text-align: center;
  }

  .loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #3498db;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 2s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .splash p {
    margin-top: 15px;
    font-size: 18px;
    font-weight: bold;
  }

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

</style>

<script>
// Mostrar/ocultar los canales digitales
document.getElementById('habilitar-canal-digital-edit').addEventListener('change', function() {
    const contenidoCanalDigital = document.getElementById('contenido-canal-digital-edit');
    if (this.checked) {
        contenidoCanalDigital.style.display = 'block';
    } else {
        contenidoCanalDigital.style.display = 'none';
    }
});

// Función para actualizar la criticidad al seleccionar una categoría
function actualizarCriticidad() {
    const selectedOption = document.getElementById('categoria_edit').options[document.getElementById('categoria_edit').selectedIndex];
    const criticidad = selectedOption ? selectedOption.getAttribute('data-criticidad') : '';
    document.getElementById('criticidad-label-edit').textContent = criticidad;
}

// Inicializar el valor de la criticidad cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarCriticidad();
});

// Actualizar el label de criticidad cuando se cambie la categoría
document.getElementById('categoria_edit').addEventListener('change', actualizarCriticidad);

// Copiar el texto del campo ivr_edit a redaccion_canales_edit
document.getElementById('mismo-canal-edit').addEventListener('change', function() {
    const ivrText = document.getElementById('ivr_edit').value;
    const canalText = document.getElementById('redaccion_canales_edit');
    
    if (this.checked) {
        canalText.value = ivrText;  // Copiar texto del IVR a los canales
    } else {
        canalText.value = '';  // Limpiar el campo si no está seleccionado
    }
});

// Cargar los datos del modal con los datos de la crisis
$(document).on('click', '.btn-warning', function() {
    var crisisId = $(this).data('id');
    
    // Mostrar el splash
    $('#splash').fadeIn();

    $.ajax({
        url: '../Controllers/crisis.php',
        method: 'POST',
        data: {accion: 3, id: crisisId},  // Trae los datos de la crisis
        success: function(data) {
            const crisisData = JSON.parse(data);
            console.log(crisisData);
            $('#id').val(crisisData.id_cyc);
            $('#no_ticket_edit').val(crisisData.no_ticket);
            $('#nombre_edit').val(crisisData.nombre);
            $('#categoria_edit').val(crisisData.categoria_cyc);
            $('#tipo_edit').val(crisisData.tipo_cyc);
            $('#ubicacion_edit').val(crisisData.ubicacion_cyc);
            $('#ivr_edit').val(crisisData.redaccion_cyc);
            $('#edit_proyecto').val(crisisData.proyecto);
            
            // Convertir la fecha de formato dd-MM-yyyy HH:mm a yyyy-MM-ddTHH:mm
            if (crisisData.fecha_programacion) {
                var fecha = crisisData.fecha_programacion.split(' ');
                var fechaPartes = fecha[0].split('-'); // [dd, MM, yyyy]
                var horaPartes = fecha[1].split(':'); // [HH, mm]
                var fechaFormateada = fechaPartes[2] + '-' + fechaPartes[1] + '-' + fechaPartes[0] + 'T' + horaPartes[0] + ':' + horaPartes[1];
                $('#fecha_programacion').val(fechaFormateada);
            }

            $('#redaccion_canales_edit').val(crisisData.redaccion_canales);

            // Marcar las opciones seleccionadas previamente en los "canales" y "bots"
            if (crisisData.canal_cyc && crisisData.canal_cyc.length > 0) {
                crisisData.canal_cyc.forEach(function(canalId) {
                    $('#canal_edit option[value="' + canalId + '"]').prop('selected', true);
                });
            }

            if (crisisData.bot_cyc && crisisData.bot_cyc.length > 0) {
                crisisData.bot_cyc.forEach(function(botId) {
                    $('#bot_edit option[value="' + botId + '"]').prop('selected', true);
                });
            }

            // Refrescar el selectpicker de nuevo para reflejar las selecciones
            $('#canal_edit').selectpicker('refresh');
            $('#bot_edit').selectpicker('refresh');

            // Mostrar el modal
            $('#editModal').modal('show');

            // Ocultar el splash una vez cargados los datos
            $('#splash').fadeOut();
        },
        error: function() {
            alert('Error al cargar los datos para la edición');
            $('#splash').fadeOut(); // Ocultar el splash en caso de error
        }
    });
});
</script>
