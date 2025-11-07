<?php
include("../Controllers/bd.php"); // Debe contener $conn (objeto mysqli)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$crisis = [];
$ubicaciones = [];
$canales = [];
$proyectos = [];
$bots = [];

$proyecto_id = $_SESSION['proyecto'] ?? null;

// Verifica que haya un proyecto en sesión
if (!$proyecto_id) {
    echo "No hay proyecto en la sesión.";
    exit;
}

// -------------------- Obtener CRISIS -------------------- //
$stmt = $conn->prepare("SELECT * FROM cat_crisis WHERE status >= 1 AND proyecto = ? ORDER BY nombre_crisis ASC");
$stmt->bind_param("s", $proyecto_id);
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $crisis = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error al obtener las crisis: " . $stmt->error;
}
$stmt->close();



// -------------------- Obtener CANALES -------------------- //
$stmt = $conn->prepare("SELECT * FROM canal_digital WHERE status >= 1 AND proyecto = ? ORDER BY nombre_canal ASC");
$stmt->bind_param("s", $proyecto_id);
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $canales = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error al obtener los canales: " . $stmt->error;
}
$stmt->close();

// -------------------- Obtener PROYECTOS -------------------- //
$stmt = $conn->prepare("SELECT * FROM cat_proyectos WHERE status >= 1 ORDER BY nombre_proyecto ASC"); // Obtener todos los proyectos
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $proyectos = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error al obtener los proyectos: " . $stmt->error;
}
$stmt->close();

// -------------------- Obtener BOTS -------------------- //
$stmt = $conn->prepare("SELECT * FROM bot WHERE status >= 1 AND proyecto = ? ORDER BY nombre_bot ASC");
$stmt->bind_param("s", $proyecto_id);
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $bots = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error al obtener los bots: " . $stmt->error;
}
$stmt->close();
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

          <!-- Sección 1 -->
          <div class="mb-4">
            <div class="row g-3 mb-3">
              <div class="col-md-2">
                <label for="categoria" class="form-label">Categoría</label>
                <select class="form-select" name="criticidad" required id="categoria">
                  <option value="">Seleccione una opción</option>
                  <?php
                  if (!empty($crisis)) {
                      foreach ($crisis as $row) {
                          echo '<option value="' . $row['id'] . '" data-criticidad="' . htmlspecialchars($row['criticidad']) . '">' . htmlspecialchars($row['nombre_crisis']) . '</option>';
                      }
                  } else {
                      echo '<option value="">No hay crisis disponibles</option>';
                  }
                  ?>
                </select>
              </div>

              <div class="col-md-2">
                <label for="tipo" class="form-label">Criticidad</label><br>
                <label for="criticidad" id="criticidad-label" style="text-align: center;" class="form-label"></label>
              </div>

              <div class="col-md-2">
                <label for="tipo" class="form-label">Tipo</label>
                <select class="form-select" required name="tipo" id="tipo">
                  <option value="">Seleccione una opción</option>
                  <option value="1">Contingencia</option>
                  <option value="2">Crisis</option>
                </select>
              </div>

              <div class="col-md-2">
                <label for="proyecto" class="form-label">Proyecto</label>
                <select class="form-select" name="proyecto" id="proyecto">
                  <option value="">Seleccione una opción</option>
                  <?php
                  if (!empty($proyectos)) {
                      foreach ($proyectos as $row) {
                          echo '<option value="' . $row['id_proyecto'] . '">' . htmlspecialchars($row['nombre_proyecto']) . '</option>';
                      }
                  } else {
                      echo '<option value="">No hay proyectos disponibles</option>';
                  }
                  ?>
                </select>
              </div>

              <div class="col-md-2">
                <label for="ubicacion" class="form-label">Ubicación de CoC</label>
                <select class="form-select" required name="ubicacion" id="ubicacion">
                  <option value="">Seleccione una opción</option>
                  <?php
                  if (!empty($ubicaciones)) {
                      foreach ($ubicaciones as $row) {
                          echo '<option value="' . $row['id_ubicacion_ivr'] . '">' . htmlspecialchars($row['nombre_ubicacion_ivr']) . '</option>';
                      }
                  } else {
                      echo '<option value="">No hay ubicaciones disponibles</option>';
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="mt-3">
              <label for="ivr" class="form-label fw-bold">Redacción para grabación en IVR</label>
              <textarea required class="form-control" name="ivr" id="ivr" rows="3"></textarea>
            </div>
          </div>

          <!-- Sección 2 -->
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="habilitar-canal-digital" id="habilitar-canal-digital">
            <label class="form-check-label" for="habilitar-canal-digital">Habilitar canal digital</label>
          </div>

          <div id="contenido-canal-digital" style="display: none;">
            <div class="row g-3">
              <div class="col-md-4">
                <select class="form-select selectpicker" id="canal" name="canal[]" multiple data-live-search="true">
                  <option selected disabled class="d-none">Canales digitales</option>
                  <?php
                  foreach ($canales as $row) {
                      echo '<option value="' . $row['nombre_canal'] . '">' . $row['nombre_canal'] . '</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-4">
                <select class="form-select selectpicker" id="bot" name="bot[]" multiple data-live-search="true">
                  <option selected disabled class="d-none">Bots</option>
                  <?php
                  foreach ($bots as $row) {
                      echo '<option value="' . $row['nombre_bot'] . '">' . $row['nombre_bot'] . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" name="mismo-canal" id="mismo-canal">
              <label class="form-check-label" for="mismo-canal">La redacción para el canal es la misma que la redacción del IVR</label>
            </div>

            <div class="mt-3">
              <label for="canal-digital-texto" class="form-label fw-bold">Redacción para grabación en canal digital</label>
              <textarea class="form-control" name="redaccion_canales" id="redaccion_canales" rows="3"></textarea>
            </div>
          </div>

      </div>
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

<script src="../js/nuevoCyC.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Mostrar/ocultar el campo de fecha_programacion
document.getElementById('programar').addEventListener('change', function() {
    const fechaBloque = document.getElementById('fecha-bloque');
    fechaBloque.style.display = this.checked ? 'block' : 'none';
});

// Validar la fecha seleccionada
document.getElementById('fecha_programacion').addEventListener('change', function() {
    const selectedDate = new Date(this.value);
    const currentDate = new Date();
    if (selectedDate < currentDate) {
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: 'La fecha y hora seleccionada no puede ser anterior a la fecha y hora actual.',
            confirmButtonText: 'Aceptar'
        });
        this.value = '';
    }
});

// Copiar texto del IVR a redaccion_canales
document.getElementById('mismo-canal').addEventListener('change', function() {
    document.getElementById('redaccion_canales').value = this.checked ? document.getElementById('ivr').value : '';
});

// Filtrar ubicaciones según proyecto seleccionado
document.getElementById('proyecto').addEventListener('change', function() {
    const proyectoId = this.value;
    const ubicacionSelect = document.getElementById('ubicacion');
    ubicacionSelect.innerHTML = '<option value="">Seleccione una opción</option>';

    if (!proyectoId) return;

    fetch(`getUbicaciones.php?proyecto=${encodeURIComponent(proyectoId)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                data.forEach(ubic => {
                    const option = document.createElement('option');
                    option.value = ubic.id_ubicacion_ivr;
                    option.textContent = ubic.nombre_ubicacion_ivr;
                    ubicacionSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No hay ubicaciones disponibles';
                ubicacionSelect.appendChild(option);
            }
        })
        .catch(error => console.error('Error al obtener ubicaciones:', error));
});

// Validación de campos y envío con SweetAlert
document.getElementById('btn-submit').addEventListener('click', function(e) {
    e.preventDefault();
    const form = document.getElementById('form-cyc');
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        input.style.borderColor = '';
        const value = String(input.value).trim();
        if ((input.tagName === 'SELECT' && (!value || input.selectedIndex === 0)) || (input.tagName !== 'SELECT' && !value)) {
            isValid = false;
            input.style.borderColor = 'red';
        }
    });

    if (!isValid) {
        Swal.fire({title:'¡Faltan campos por llenar!',text:'Por favor, completa todos los campos obligatorios.',icon:'error',confirmButtonText:'Entendido'});
    } else {
        Swal.fire({
            title: '¿Estás seguro de generar el registro?',
            text: "¡Este registro será definitivo!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then(result => {
            if (result.isConfirmed) form.submit();
            else Swal.fire('Cancelado','No se ha realizado el registro','error');
        });
    }
});

// Limpiar el formulario al cerrar el modal
document.getElementById('loginModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('form-cyc').reset();
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
