$(document).ready(function () {
  var table = $("#table-ubicaciones").DataTable({
    ajax: {
      url: "../Controllers/catUbicaciones.php",
      method: "POST",
      data: {
        accion: 2, // Acción para obtener los datos de ubicación IVR
      },
      dataSrc: function (json) {
        var rows = [];
        $.each(json, function (index, item) {
          // Cambiar el icono de estado dependiendo del valor de "status"
          var iconoStatus =
            item.status === 1
              ? `<img src="../iconos/activo.png" alt="Activo" style="width: 50px; height: 25px;" data-status="1" data-id="${item.id}" onclick="toggleStatus(${item.id}, this, ${item.status})">`
              : `<img src="../iconos/desactivo.png" alt="Desactivado" style="width: 50px; height: 25px;" data-status="0" data-id="${item.id}" onclick="toggleStatus(${item.id}, this, ${item.status})">`;

          // Crear las filas de la tabla
          rows.push([
            item.id, // ID de la ubicación IVR
            item.nombre_ubicacion_ivr, // Nombre de la ubicación IVR
            item.nombre_proyecto, // Asegúrate que el JSON también envía este campo, si no, ajusta PHP
            `  
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarUbicacionIVR" 
                data-id="${item.id}" 
                data-nombre="${item.nombre_ubicacion_ivr}" 
                data-status="${item.status}" 
                data-proyecto="${item.proyecto}" 
                style="background: transparent; border: none;">
                  <img src="../iconos/edit.png" alt="Editar" style="width: 20px; height: 20px;">
              </button>
              <button class="btn btn-danger btn-sm" onclick="deleteUbicacionIVR(${item.id})" style="background: transparent; border: none;">
                  <img src="../iconos/delete.png" alt="Eliminar" style="width: 20px; height: 20px;">
              </button>
            `, // Fin de la columna de acciones
          ]);
        });
        return rows;
      },
    },
    processing: true,
    language: {
      processing:
        "<div class='loading-overlay'><div class='loader'></div></div>",
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      loadingRecords: "Cargando...",
      zeroRecords: "No se encontraron resultados",
      emptyTable: "No hay datos disponibles en la tabla",
      paginate: {
        first: "Primero",
        previous: "Anterior",
        next: "Siguiente",
        last: "Último",
      },
      aria: {
        sortAscending: ": activar para ordenar la columna de manera ascendente",
        sortDescending:
          ": activar para ordenar la columna de manera descendente",
      },
    },
    dom: "ptlr",
    searching: true,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    pageLength: 10,
  });

  // Manejar el evento de clic en el botón de editar
  $("#table-ubicaciones").on(
    "click",
    'button[data-bs-target="#modalEditarUbicacionIVR"]',
    function () {
      var ubicacionData = {
        id: $(this).data("id"),
        nombre_ubicacion_ivr: $(this).data("nombre"),
        status: $(this).data("status"),
        proyecto: $(this).data("proyecto"), // <-- Agregado proyecto
      };

      // Cargar los datos en el formulario de edición
      cargarDatosUbicacionIVR(ubicacionData);
    }
  );

  // Cargar los datos en el modal de edición
  function cargarDatosUbicacionIVR(ubicacionData) {
    $("#edit_id_ubicacion_ivr").val(ubicacionData.id);
    $("#edit_nombre_ubicacion_ivr").val(
      ubicacionData.nombre_ubicacion_ivr || ""
    );
    $("#accion_editar").val("3");

    // Seleccionar el proyecto correcto en el combo
    if (ubicacionData.proyecto) {
      $("#edit_proyecto").val(ubicacionData.proyecto);
    }
  }
});
function cargarProyectos(selectId) {
  fetch("../Controllers/catUbicaciones.php?accion=6")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Respuesta de red no OK");
      }
      return response.json();
    })
    .then((proyectos) => {
      const select = document.getElementById(selectId);
      select.innerHTML = '<option value="">Seleccionar proyecto</option>';

      proyectos.forEach((proyecto) => {
        const option = document.createElement("option");
        option.value = proyecto.id_proyecto; // usa el ID, no el nombre
        option.textContent = proyecto.nombre_proyecto;
        select.appendChild(option);
      });
    })
    .catch((error) => {
      console.error("Error al cargar proyectos:", error);
    });
}
function deleteUbicacionIVR(id) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Esta acción desactivará la ubicación IVR.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Enviar la solicitud al controlador PHP
      fetch(`../Controllers/catUbicaciones.php?accion=4&id=${id}`, {
        method: "GET",
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Error en la respuesta del servidor");
          }
          return response.text();
        })
        .then((data) => {
          // Mostrar mensaje de éxito y recargar la tabla
          Swal.fire({
            title: "Eliminado",
            text: "La ubicación IVR ha sido desactivada correctamente.",
            icon: "success",
            confirmButtonText: "Aceptar",
          }).then(() => {
            // Recargar la tabla sin refrescar toda la página
            $("#table-ubicaciones").DataTable().ajax.reload(null, false);
          });
        })
        .catch((error) => {
          console.error("Error al eliminar:", error);
          Swal.fire({
            title: "Error",
            text: "No se pudo eliminar la ubicación IVR.",
            icon: "error",
            confirmButtonText: "Aceptar",
          });
        });
    }
  });
}
