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
        <form action="../Controllers/crisis.php" method="POST" id="form-cyc-edit">
          <input type="hidden" name="accion" id="accion" value="4">
          <input type="hidden" name="id" id="id">

          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <input type="text" name="no_ticket" readonly id="no_ticket_edit" required class="form-control" placeholder="Número de ticket">
            </div>
            <div class="col-md-9">
              <input type="text" name="nombre" id="nombre_edit" required class="form-control" placeholder="Nombre">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <input class="form-check-input" checked name="programar" id="programar_edit" type="checkbox">
              <label class="form-check-label" style="padding-top: 2px;" for="programar_edit">Programar CoC</label>
            </div>
            <div class="col-md-3" id="fecha-bloque-edit">
              <div class="input-group">
                <input type="datetime-local" name="fecha_programacion" class="form-control" id="fecha_programacion_2">
              </div>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-2">
              <select class="form-select" name="categoria" required id="categoria_edit">
                <option selected disabled class="d-none">Categoría</option>
                <?php foreach ($crisis as $row) : ?>
                  <option value="<?= $row['id']; ?>" data-criticidad="<?= $row['criticidad']; ?>"><?= $row['nombre_crisis']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-2">
              <label id="criticidad-label-edit" class="form-label text-center"></label>
            </div>

            <div class="col-md-2">
              <select class="form-select" required name="tipo" id="tipo_edit">
                <option selected disabled class="d-none">Tipo</option>
                <option value="1">Contingencia</option>
                <option value="2">Crisis</option>
              </select>
            </div>

            <div class="col-md-2">
              <select class="form-select" required name="proyecto" id="edit_proyecto">
                <option selected disabled class="d-none">Proyecto</option>
                <?php foreach ($proyectos as $row_proyecto) : ?>
                  <option value="<?= $row_proyecto['id_proyecto']; ?>"><?= $row_proyecto['nombre_proyecto']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-2">
              <select class="form-select" required name="ubicacion" id="ubicacion_edit">
                <option selected disabled class="d-none">Ubicación</option>
                <?php foreach ($ubicaciones as $row) : ?>
                  <option value="<?= $row['id_ubicacion_ivr']; ?>" data-proyecto="<?= $row['proyecto_id']; ?>"><?= $row['nombre_ubicacion_ivr']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mt-3">
            <label for="ivr_edit" class="form-label fw-bold">Redacción para grabación en IVR</label>
            <textarea required class="form-control" name="ivr" id="ivr_edit" rows="3"></textarea>
          </div>

          <div class="modal-footer d-flex justify-content-center">
            <div class="btn-container">
              <button type="submit" class="btn-icon" style="border-radius: 15px;">
                <span>Guardar y habilitar</span>
                <img src="../iconos/guardar.png" alt="Guardar">
              </button>
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


<style type="text/css">
  /* Estilos (sin cambios) */
  .splash {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.7); display: none; justify-content: center; align-items: center; z-index: 9999; text-align: center;
  }
  .loader {
    border: 16px solid #f3f3f3; border-top: 16px solid #3498db; border-radius: 50%; width: 60px; height: 60px; animation: spin 2s linear infinite;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .splash p { margin-top: 15px; font-size: 18px; font-weight: bold; }
  .modal-footer { display: flex; justify-content: center; align-items: center; }
  .btn-container { display: flex; justify-content: center; align-items: center; gap: 20px; }
  .btn-icon { display: flex; align-items: center; justify-content: center; padding: 10px 20px; gap: 10px; background-color: #4B4A4B; border: none; color: white; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease; }
  .btn-icon:hover { background-color: #5A595A; }
  .btn-icon img { height: 24px; width: 24px; }
</style>

<script>
$(document).on('click', '.btn-warning', function () {
    var crisisId = $(this).data('id');
    console.log('ID de crisis a editar:', crisisId);

    $('#splash').fadeIn();

    $.ajax({
        url: '../Controllers/crisis.php',
        method: 'POST',
        dataType: 'json',
        data: { accion: 3, id: crisisId },
        success: function (crisisData) {
            console.log('Datos recibidos:', crisisData);

            $('#id').val(crisisData.id_cyc);
            $('#no_ticket_edit').val(crisisData.no_ticket);
            $('#nombre_edit').val(crisisData.nombre);

            $('#categoria_edit').val(String(crisisData.categoria_cyc));
            $('#tipo_edit').val(String(crisisData.tipo_cyc));
            $('#ubicacion_edit').val(String(crisisData.ubicacion_cyc));
            $('#edit_proyecto').val(String(crisisData.proyecto));

            $('#ivr_edit').val(crisisData.redaccion_cyc || '');
            $('#redaccion_canales_edit').val(crisisData.redaccion_canales || '');

            // Función para actualizar la criticidad (asumiendo que existe en tu JS)
            if (typeof actualizarCriticidad === 'function') {
                actualizarCriticidad();
            }

            if (crisisData.fecha_programacion) {
                const fechaHora = crisisData.fecha_programacion.slice(0, 16).replace(' ', 'T');
                $('#fecha_programacion_2').val(fechaHora);
            } else {
                $('#fecha_programacion_2').val('');
            }
            
            const canal = Array.isArray(crisisData.canal_cyc) ? crisisData.canal_cyc : [];
            const bot = Array.isArray(crisisData.bot_cyc) ? crisisData.bot_cyc : [];

            $('#canal_edit').val(canal);
            $('#bot_edit').val(bot);

            if ($('.selectpicker').length) {
                $('.selectpicker').selectpicker('refresh');
            }

            const tieneCanalDigital = (canal.length > 0) || (bot.length > 0) || crisisData.redaccion_canales;
            
            if(tieneCanalDigital) {
                $('#habilitar-canal-digital-edit').prop('checked', true);
                $('#contenido-canal-digital-edit').show();
            } else {
                 $('#habilitar-canal-digital-edit').prop('checked', false);
                $('#contenido-canal-digital-edit').hide();
            }

            setTimeout(() => {
                $('#editModal').modal('show');
                $('#splash').fadeOut();
            }, 300);
        },
        error: function (xhr, status, error) {
            console.error('Error en AJAX:', error);
            console.log('Respuesta del servidor:', xhr.responseText);
            alert('Error al cargar los datos para la edición.');
            $('#splash').fadeOut();
        }
    });
});

// Listener para el checkbox de habilitar canal digital
$('#habilitar-canal-digital-edit').on('change', function() {
    if ($(this).is(':checked')) {
        $('#contenido-canal-digital-edit').slideDown();
    } else {
        $('#contenido-canal-digital-edit').slideUp();
    }
});
</script>