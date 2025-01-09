//Fecha y hora para programar 

document.addEventListener("DOMContentLoaded", function () {
  const checkbox = document.getElementById("programar");
  const fechaBloque = document.getElementById("fecha-bloque");
  const fechaInput = document.getElementById("fecha");

  checkbox.addEventListener("change", function () {
    if (checkbox.checked) {
      fechaBloque.style.display = "block"; // Mostrar el bloque
    } else {
      fechaBloque.style.display = "none"; // Ocultar el bloque
      fechaInput.value = null; // Restablecer el valor a null
    }
  });
});

//Mostrar menu para canales 
document.addEventListener("DOMContentLoaded", function () {
  const checkboxCanalDigital = document.getElementById("habilitar-canal-digital");
  const contenidoCanalDigital = document.getElementById("contenido-canal-digital");
  const canalSelect = document.getElementById("canal");
  const botSelect = document.getElementById("bot");
  const mismoCanalCheckbox = document.getElementById("mismo-canal");
  const canalDigitalTexto = document.getElementById("canal-digital-texto");

  checkboxCanalDigital.addEventListener("change", function () {
    if (checkboxCanalDigital.checked) {
      contenidoCanalDigital.style.display = "block"; // Mostrar el contenido
    } else {
      contenidoCanalDigital.style.display = "none"; // Ocultar el contenido
      // Limpiar los valores de los campos
      canalSelect.value = "";
      botSelect.value = "";
      mismoCanalCheckbox.checked = false;
      canalDigitalTexto.value = "";
    }
  });
});


//Replicar texto 
document.addEventListener("DOMContentLoaded", function() {
  const ivrTextArea = document.getElementById("ivr");
  const canalTextoArea = document.getElementById("canal-digital-texto");
  const mismoCanalCheck = document.getElementById("mismo-canal");

  // Función para copiar el texto de IVR a Canal Digital y deshabilitar el campo
  function actualizarCanalDigital() {
    if (mismoCanalCheck.checked) {
      canalTextoArea.value = ivrTextArea.value;
      canalTextoArea.disabled = true;
    } else {
      
      canalTextoArea.disabled = false;
    }
  }

  // Evento cuando se cambia el estado del checkbox "La redacción para el canal es la misma que la redacción del IVR"
  mismoCanalCheck.addEventListener("change", actualizarCanalDigital);

  // Asegurarse de actualizar el campo al cargar la página si ya está seleccionado el checkbox
  actualizarCanalDigital();
});


document.getElementById('categoria').addEventListener('change', function() {
  var selectedOption = this.options[this.selectedIndex]; // Obtener la opción seleccionada
  var criticidad = selectedOption.getAttribute('data-criticidad'); // Obtener la criticidad asociada

  // Actualizar el texto del label
  document.getElementById('criticidad-label').textContent =criticidad;
});



document.getElementById('categoria').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex]; // Obtener la opción seleccionada
    var criticidad = selectedOption.getAttribute('data-criticidad'); // Obtener la criticidad asociada

    // Actualizar el texto del label
    document.getElementById('criticidad-label').textContent = criticidad;

    function cambiarColorCriticidad(select) {
  const selectedValue = select.value;
  const selectedOption = select.options[select.selectedIndex];
  const categoria = selectedOption.dataset.nombre;
  const criticidadSelect = document.getElementById('categoria');

  // Cambiar color de fondo según la criticidad
  if (categoria === 'Alta') {
    criticidadSelect.classList.remove('bg-warning', 'bg-success');
    criticidadSelect.classList.add('bg-danger');
  } else if (categoria === 'Media') {
    criticidadSelect.classList.remove('bg-danger', 'bg-success');
    criticidadSelect.classList.add('bg-warning');
  } else if (categoria === 'Baja') {
    criticidadSelect.classList.remove('bg-danger', 'bg-warning');
    criticidadSelect.classList.add('bg-success');
  }
}
  });


$(document).ready(function() {
      $('.selectpicker').selectpicker({
          noneSelectedText: 'Seleccione uno o más canales', // Texto inicial
          liveSearch: true, // Habilita la búsqueda dentro del dropdown
          multipleSeparator: ', ', // Separador para múltiples selecciones
          style: 'btn-light' // Estilo del botón (puedes usar clases de Bootstrap)
      });
  });


// Validar que la fecha de programación no sea anterior a la fecha actual
document.getElementById('form-cyc').addEventListener('submit', function(event) {
    const fechaProgramacion = document.getElementById('fecha_programacion').value;
    const currentDateTime = new Date().toISOString().slice(0, 16); // Formato YYYY-MM-DDTHH:MM

    if (fechaProgramacion && fechaProgramacion < currentDateTime) {
        alert('La fecha de programación no puede ser anterior a la fecha y hora actual.');
        event.preventDefault(); // Evitar el envío del formulario
    }
});