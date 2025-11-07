$(document).ready(function () {
  // ----- CONFIGURACIÓN DE LA TABLA -----
  var table = $("#table-ubicaciones").DataTable({
    ajax: {
      url: "../Controllers/catUbicaciones.php",
      method: "POST",
      data: { accion: 2 },
      dataSrc: function (json) {
        var rows = [];
        $.each(json, function (index, item) {
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
    dom: "ptlr",
    pageLength: 10,
    language: {
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      emptyTable: "No hay datos disponibles en la tabla",
    },
  });

  // ----- EVENTO EDITAR -----
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

  // Función interna para cargar datos en el modal
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

// --------------------------
// 🚀 FUNCIONES GLOBALES
// --------------------------

// Cargar proyectos dinámicamente
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

// 🗑️ Eliminar (borrado lógico)
function deleteUbicacionIVR(id, proyecto) {
  console.log("Intentando eliminar:", id, proyecto);

  if (typeof Swal === "undefined") {
    alert("SweetAlert2 no está cargado");
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
            Swal.fire("Eliminado", data.message, "success").then(() => {
              $("#table-ubicaciones").DataTable().ajax.reload(null, false);
            });
          } else {
            Swal.fire("Error", data.message || "No se pudo eliminar", "error");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire("Error", "Error de conexión con el servidor.", "error");
        });
    }
  });
}
