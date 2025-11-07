<?php
include("../Controllers/bd.php");

$proyecto_id = $_GET['proyecto'] ?? null;
if (!$proyecto_id) {
    echo json_encode(['error' => 'No se recibió proyecto']);
    exit;
}

$data = [];

// CRISIS
$stmt = $conn->prepare("SELECT id, nombre_crisis, criticidad FROM cat_crisis WHERE status >= 1 AND proyecto = ? ORDER BY nombre_crisis ASC");
$stmt->bind_param("s", $proyecto_id);
$stmt->execute();
$data['crisis'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// UBICACIONES
$stmt = $conn->prepare("SELECT id_ubicacion_ivr, nombre_ubicacion_ivr FROM ubicacion_ivr WHERE status >= 1 AND proyecto = ?");
$stmt->bind_param("s", $proyecto_id);
$stmt->execute();
$data['ubicaciones'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// CANALES
$stmt = $conn->prepare("SELECT nombre_canal FROM canal_digital WHERE status >= 1 AND proyecto = ? ORDER BY nombre_canal ASC");
$stmt->bind_param("s", $proyecto_id);
$stmt->execute();
$data['canales'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// BOTS
$stmt = $conn->prepare("SELECT nombre_bot FROM bot WHERE status >= 1 AND proyecto = ? ORDER BY nombre_bot ASC");
$stmt->bind_param("s", $proyecto_id);
$stmt->execute();
$data['bots'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($data);

?>


<?php
include("../Controllers/bd.php"); // Debe contener $conn (objeto mysqli)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Nueva CyC</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulario -->
        <form action="../Controllers/crisis.php" method="POST" id="form-cyc">
          <input type="text" name="accion" id="accion" hidden value="1">

          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <input type="text" name="no_ticket" id="no_ticket" required class="form-control" placeholder="No. de ticket" aria-label="No. de ticket">
            </div>
            <div class="col-md-9">
              <input type="text" name="nombre" id="nombre" required class="form-control" placeholder="Nombre" aria-label="Nombre">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <input class="form-check-input" name="programar" id="programar" type="checkbox">
              <label class="form-check-label" style="padding-top: 2px;" for="programar">Programar CoC</label>
            </div>
            <div class="col-md-3" id="fecha-bloque" style="display: none;">
              <div class="input-group">
                <input type="datetime-local" name="fecha_programacion" class="form-control" id="fecha_programacion" aria-describedby="fecha-icono">
              </div>
            </div>
          </div>

          <!-- Selección de proyecto -->
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label for="proyecto" class="form-label">Proyecto</label>
              <select class="form-select" name="proyecto" id="proyecto" required>
                <option value="">Seleccione un proyecto</option>
                <?php
                  $proyectos_query = $conn->query("SELECT id_proyecto, nombre_proyecto FROM cat_proyectos WHERE status >= 1 ORDER BY nombre_proyecto ASC");
                  while ($row = $proyectos_query->fetch_assoc()) {
                      echo '<option value="' . $row['id_proyecto'] . '">' . htmlspecialchars($row['nombre_proyecto']) . '</option>';
                  }
                ?>
              </select>
            </div>
          </div>

          <!-- Sección de combos dependientes -->
          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <label for="categoria" class="form-label">Categoría</label>
              <select class="form-select" name="criticidad" id="categoria" required>
                <option value="">Seleccione un proyecto primero</option>
              </select>
            </div>

            <div class="col-md-3">
              <label for="ubicacion" class="form-label">Ubicación de CoC</label>
              <select class="form-select" name="ubicacion" id="ubicacion" required>
                <option value="">Seleccione un proyecto primero</option>
              </select>
            </div>

            <div class="col-md-3">
              <label for="canal" class="form-label">Canales digitales</label>
              <select class="form-select selectpicker" name="canal[]" id="canal" multiple data-live-search="true">
                <option selected disabled class="d-none">Seleccione un proyecto primero</option>
              </select>
            </div>

            <div class="col-md-3">
              <label for="bot" class="form-label">Bots</label>
              <select class="form-select selectpicker" name="bot[]" id="bot" multiple data-live-search="true">
                <option selected disabled class="d-none">Seleccione un proyecto primero</option>
              </select>
            </div>
          </div>

          <!-- IVR -->
          <div class="mt-3">
            <label for="ivr" class="form-label fw-bold">Redacción para grabación en IVR</label>
            <textarea required class="form-control" name="ivr" id="ivr" rows="3"></textarea>
          </div>

          <!-- Canal digital -->
          <div class="form-check mb-3 mt-3">
            <input class="form-check-input" type="checkbox" name="habilitar-canal-digital" id="habilitar-canal-digital">
            <label class="form-check-label" for="habilitar-canal-digital">Habilitar canal digital</label>
          </div>

          <div id="contenido-canal-digital" style="display: none;">
            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" name="mismo-canal" id="mismo-canal">
              <label class="form-check-label" for="mismo-canal">La redacción para el canal es la misma que la redacción del IVR</label>
            </div>
            <div class="mt-3">
              <label for="canal-digital-texto" class="form-label fw-bold">Redacción para grabación en canal digital</label>
              <textarea class="form-control" name="redaccion_canales" id="redaccion_canales" rows="3"></textarea>
            </div>
          </div>

          <!-- Botones -->
          <div class="modal-footer d-flex justify-content-center">
            <div class="btn-container">
              <button type="button" class="btn-icon" style="border-radius: 15px;" id="btn-submit">
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

<!-- Script AJAX para llenar combos -->
<script>
document.getElementById('proyecto').addEventListener('change', function() {
    const proyectoId = this.value;
    if (!proyectoId) return;

    fetch(`getProyectoData.php?proyecto=${proyectoId}`)
        .then(res => res.json())
        .then(data => {
            // CRISIS
            const crisisSelect = document.getElementById('categoria');
            crisisSelect.innerHTML = '<option value="">Seleccione una opción</option>';
            data.crisis.forEach(c => {
                crisisSelect.innerHTML += `<option value="${c.id}" data-criticidad="${c.criticidad}">${c.nombre_crisis}</option>`;
            });

            // UBICACIONES
            const ubicacionSelect = document.getElementById('ubicacion');
            ubicacionSelect.innerHTML = '<option value="">Seleccione una opción</option>';
            data.ubicaciones.forEach(u => {
                ubicacionSelect.innerHTML += `<option value="${u.id_ubicacion_ivr}">${u.nombre_ubicacion_ivr}</option>`;
            });

            // CANALES
            const canalSelect = document.getElementById('canal');
            canalSelect.innerHTML = '';
            data.canales.forEach(c => {
                canalSelect.innerHTML += `<option value="${c.nombre_canal}">${c.nombre_canal}</option>`;
            });

            // BOTS
            const botSelect = document.getElementById('bot');
            botSelect.innerHTML = '';
            data.bots.forEach(b => {
                botSelect.innerHTML += `<option value="${b.nombre_bot}">${b.nombre_bot}</option>`;
            });

            // Actualizar selectpicker si usas Bootstrap
            if (typeof $('.selectpicker') !== 'undefined') $('.selectpicker').selectpicker('refresh');
        });
});
</script>


<!-- Enlace al archivo JS -->
<script src="../js/nuevoCyC.js"></script>


<!-- Script de SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.getElementById('btn-submit').addEventListener('click', function (e) {
    e.preventDefault();  // Prevenir el envío inmediato del formulario

    // Limpiar estilos previos (si hay bordes rojos por campos incompletos)
    const form = document.getElementById('form-cyc');
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    inputs.forEach(input => {
      input.style.borderColor = '';  // Limpiar cualquier borde rojo previo
    });

    let isValid = true;
    let missingFields = [];

    //document.getElementById('form-cyc').submit();

    // Validar los campos requeridos
    inputs.forEach(input => {
      let value = String(input.value).trim(); // Usar el valor "limpio"

      // Validación para SELECT (si no se selecciona una opción válida)
      if (input.tagName === 'SELECT' && (!value || input.selectedIndex === 0 || value.includes("Categoría") || value.includes("Tipo") || value.includes("Ubicación"))) { 
        console.log(`Campo: ${input.name}, Valor detectado: "${value}", Índice seleccionado: ${input.selectedIndex}`);
        isValid = false;
        missingFields.push(input);
        input.style.borderColor = 'red';  // Resaltar con borde rojo
      }
      // Validación para INPUT y TEXTAREA (si está vacío)
      else if ((input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') && !value) {
        isValid = false;
        missingFields.push(input);
        input.style.borderColor = 'red';  // Resaltar con borde rojo
      }
    });

    // Si hay campos faltantes, mostrar el SweetAlert de error
    if (!isValid) {
      Swal.fire({
        title: '¡Faltan campos por llenar!',
        text: 'Por favor, completa todos los campos obligatorios.',
        icon: 'error',
        confirmButtonText: 'Entendido'
      });
    } else {
      // Si todos los campos están completos, mostrar SweetAlert de confirmación
      Swal.fire({
        title: '¿Estás seguro de generar el registro?',
        text: "¡Este registro será definitivo!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          // Si el usuario confirma, envía el formulario
          document.getElementById('form-cyc').submit();
        } else {
          // Si el usuario cancela, no hace nada
          Swal.fire('Cancelado', 'No se ha realizado el registro', 'error');
        }
      });
    }
  });
</script>



<script>
// Mostrar/ocultar el campo de fecha_programacion
document.getElementById('programar').addEventListener('change', function() {
    const fechaBloque = document.getElementById('fecha-bloque');
    if (this.checked) {
        fechaBloque.style.display = 'block';
    } else {
        fechaBloque.style.display = 'none';
    }
});

// Validar la fecha seleccionada y mostrar un SweetAlert si la fecha es anterior a la fecha y hora actual
document.getElementById('fecha_programacion').addEventListener('change', function() {
    const selectedDate = new Date(this.value);
    const currentDate = new Date();

    if (selectedDate < currentDate) {
        // Mostrar el mensaje con SweetAlert
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: 'La fecha y hora seleccionada no puede ser anterior a la fecha y hora actual.',
            confirmButtonText: 'Aceptar'
        });

        // Limpiar el valor del input de fecha
        this.value = '';
    }
});

// Función para copiar el texto del campo ivr a redaccion_canales
document.getElementById('mismo-canal').addEventListener('change', function() {
    const ivrText = document.getElementById('ivr').value; // Obtén el contenido del text area de ivr
    const redaccionCanales = document.getElementById('redaccion_canales'); // Obtén el text area de redaccion_canales

    if (this.checked) {
        // Si el checkbox está marcado, copia el texto de ivr a redaccion_canales
        redaccionCanales.value = ivrText;
    } else {
        // Si el checkbox está desmarcado, limpia el campo de redaccion_canales
        redaccionCanales.value = '';
    }
});

// Limpiar los campos del formulario al cerrar el modal
document.getElementById('loginModal').addEventListener('hidden.bs.modal', function () {
    // Limpiar todos los campos del formulario
    document.getElementById('form-cyc').reset();

    // Si hay algún campo específico que quieras limpiar después de cerrar, lo puedes hacer aquí
    document.getElementById('fecha-bloque').style.display = 'none';  
    document.getElementById('contenido-canal-digital').style.display = 'none'; 
});
</script>


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
</style>
