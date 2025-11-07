$(document).ready(function () {
  var table = $("#table-ubicaciones").DataTable({
    ajax: {
      url: "../Controllers/catUbicaciones.php",
      method: "POST",
      data: {
        accion: 2,
      },
      dataSrc: function (json) {
        var rows = [];
        $.each(json, function (index, item) {
          var iconoStatus =
            item.status === 1
              ? `<img src="../iconos/activo.png" alt="Activo" style="width: 50px; height: 25px;" data-status="1" data-id="${item.id}" onclick="toggleStatus(${item.id}, this, ${item.status})">`
              : `<img src="../iconos/desactivo.png" alt="Desactivado" style="width: 50px; height: 25px;" data-status="0" data-id="${item.id}" onclick="toggleStatus(${item.id}, this, ${item.status})">`;

          // Botones de acción
          const proyectoSeguro = item.proyecto
            ? item.proyecto.replace(/'/g, "\\'")
            : "";

          rows.push([
            item.id,
            item.nombre_ubicacion_ivr,
            item.nombre_proyecto,
            `
              <button class="btn btn-warning btn-sm" 
                data-bs-toggle="modal" 
                data-bs-target="#modalEditarUbicacionIVR" 
                data-id="${item.id}" 
                data-nombre="${item.nombre_ubicacion_ivr}" 
                data-status="${item.status}" 
                data-proyecto="${item.proyecto}" 
                style="background: transparent; border: none;">
                  <img src="../iconos/edit.png" alt="Editar" style="width: 20px; height: 20px;">
              </button>
              <button class="btn btn-danger btn-sm" 
                onclick="deleteUbicacionIVR(${item.id}, '${proyectoSeguro}')" 
                style="background: transparent; border: none;">
                  <img src="../iconos/delete.png" alt="Eliminar" style="width: 20px; height: 20px;">
              </button>
            `,
          ]);
        });
        return rows;
      },
    },
    processing: true,
    language: {
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      zeroRecords: "No se encontraron resultados",
      emptyTable: "No hay datos disponibles en la tabla",
      paginate: {
        first: "Primero",
        previous: "Anterior",
        next: "Siguiente",
        last: "Último",
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

  // Botón de editar
  $("#table-ubicaciones").on(
    "click",
    'button[data-bs-target="#modalEditarUbicacionIVR"]',
    function () {
      var ubicacionData = {
        id: $(this).data("id"),
        nombre_ubicacion_ivr: $(this).data("nombre"),
        status: $(this).data("status"),
        proyecto: $(this).data("proyecto"),
      };
      cargarDatosUbicacionIVR(ubicacionData);
    }
  );

  function cargarDatosUbicacionIVR(ubicacionData) {
    $("#edit_id_ubicacion_ivr").val(ubicacionData.id);
    $("#edit_nombre_ubicacion_ivr").val(
      ubicacionData.nombre_ubicacion_ivr || ""
    );
    $("#accion_editar").val("3");
    if (ubicacionData.proyecto) {
      $("#edit_proyecto").val(ubicacionData.proyecto);
    }
  }
});

// Cargar proyectos
function cargarProyectos(selectId) {
  fetch("../Controllers/catUbicaciones.php?accion=6")
    .then((response) => response.json())
    .then((proyectos) => {
      const select = document.getElementById(selectId);
      select.innerHTML = '<option value="">Seleccionar proyecto</option>';
      proyectos.forEach((proyecto) => {
        const option = document.createElement("option");
        option.value = proyecto.id_proyecto;
        option.textContent = proyecto.nombre_proyecto;
        select.appendChild(option);
      });
    })
    .catch((error) => console.error("Error al cargar proyectos:", error));
}

// 🗑️ Función de borrado lógico
function deleteUbicacionIVR(id, proyecto) {
  console.log("Intentando eliminar ID:", id, "Proyecto:", proyecto);

  if (typeof Swal === "undefined") {
    alert("SweetAlert2 no está cargado. Asegúrate de incluir el script.");
    return;
  }

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
      fetch(
        `../Controllers/catUbicaciones.php?accion=4&id=${id}&proyecto=${proyecto}`
      )
        .then((response) => response.json())
        .then((data) => {
          console.log("Respuesta del servidor:", data);
          if (data.success) {
            Swal.fire({
              title: "Eliminado",
              text: "La ubicación IVR ha sido desactivada correctamente.",
              icon: "success",
            }).then(() => {
              $("#table-ubicaciones").DataTable().ajax.reload(null, false);
            });
          } else {
            Swal.fire("Error", data.message || "No se pudo eliminar.", "error");
          }
        })
        .catch((error) => {
          console.error("Error en la solicitud:", error);
          Swal.fire("Error", "Error de conexión con el servidor.", "error");
        });
    }
  });
}
