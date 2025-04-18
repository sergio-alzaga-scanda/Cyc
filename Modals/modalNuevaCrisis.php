<?php
include("../Controllers/bd.php");

// Obtener las crisis
$queryCrisis = "SELECT * FROM cat_crisis WHERE status > 0 ORDER BY nombre_crisis ASC";
$crisis_data = $conn->prepare($queryCrisis);

if ($crisis_data->execute()) {
    $crisis = $crisis_data->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros
} else {
    echo "Error al obtener las crisis";
}

// Obtener las ubicaciones
$queryUbicaciones = "SELECT * FROM ubicacion_ivr WHERE status > 0";
$ubicacion = $conn->prepare($queryUbicaciones);

if ($ubicacion->execute()) {
    $ubicaciones = $ubicacion->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros
} else {
    echo "Error al obtener las ubicaciones";
}

// Obtener los canales
$queryCanales = "SELECT * FROM canal_digital WHERE status > 0 ORDER BY nombre_canal ASC";
$canal = $conn->prepare($queryCanales);

if ($canal->execute()) {
    $canales = $canal->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros
} else {
    echo "Error al obtener los canales";
}

// Obtener los proyectos
$queryProyecto = "SELECT * FROM cat_proyectos WHERE status > 0 ORDER BY nombre_proyecto ASC";
$proyecto = $conn->prepare($queryProyecto);

if ($proyecto->execute()) {
    $proyectos = $proyecto->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros
} else {
    echo "Error al obtener los proyectos";
}

// Obtener los bots
$queryBot = "SELECT * FROM bot ORDER BY nombre_bot ASC";
$bot = $conn->prepare($queryBot);

if ($bot->execute()) {
    $bots = $bot->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros
} else {
    echo "Error al obtener los bots";
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
              <input class="form-check-input" name="programar" id="programar" type="checkbox" id="programar">
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
  <label for="categoria" class="form-label">Categoria</label>
  <select class="form-select" name="criticidad" required id="categoria">
    <option value="">Seleccione una opción</option> <!-- Opción vacía -->
    <?php
      foreach ($crisis as $row) {
          echo '<option value="' . $row['id'] . '" data-criticidad="' . $row['criticidad'] . '">' . $row['nombre_crisis'] . '</option>';
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
  <select class="form-select" required  name="tipo" id="tipo">
    <option value="">Seleccione una opción</option> <!-- Opción vacía -->
    <option value="1">Contingencia</option>
    <option value="2">Crisis</option>
  </select>
</div>

<div class="col-md-2">
  <label for="ubicacion" class="form-label">Ubicación de CoC</label>
  <select class="form-select" required  name="ubicacion" id="ubicacion">
    <option value="">Seleccione una opción</option> <!-- Opción vacía -->
    <?php
      foreach ($ubicaciones as $row) {
          echo '<option value="' . $row['id_ubicacion_ivr'] . '">' . $row['nombre_ubicacion_ivr'] . '</option>';
      }
    ?>
  </select>
</div>

<!-- Nuevo combo agregado para 'Prioridad' -->
<div class="col-md-2">
  <label for="proyecto" class="form-label">Proyecto</label>
  <select class="form-select"  name="proyecto" id="proyecto">
    <option value="">Seleccione una opción</option> <!-- Opción vacía -->
    <?php
      foreach ($proyectos as $row_proyecto) {
          echo '<option value="' . $row_proyecto['id_proyecto'] . '">' . $row_proyecto['nombre_proyecto'] . ' </option>';
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

          <!-- Botones -->
      </div>
      <div class="modal-footer d-flex justify-content-center">
     <div class="btn-container">
        <!-- Botón Guardar y habilitar -->
        <button type="button" class="btn-icon" style="border-radius: 15px;" id="btn-submit">
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
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

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
