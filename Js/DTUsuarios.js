$(document).ready(function () {
  var table = $("#usuariosTable").DataTable({
    ajax: {
      url: "../Controllers/usuarios.php",
      method: "POST",
      data: { accion: 2 },
      dataSrc: function (json) {
        var rows = [];
        $.each(json, function (index, item) {
          var iconoStatus = item.status === "1";

          rows.push([
            item.idUsuarios,
            item.nombre_usuario,
            item.correo_usuario,
            item.puesto_usuario,
            item.nombre_perfil,
            `  
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditUsuarios"
                data-id="${item.idUsuarios}"
                data-nombre="${item.nombre_usuario}"
                data-correo="${item.correo_usuario}"
                data-puesto="${item.puesto_usuario}"
                data-perfil="${item.perfil_usuario}"
                data-telefono="${item.telefono_usuario}"
                data-status="${item.status}"
                data-proyecto="${item.id_proyecto}"  <!-- Cambiado a id_proyecto -->
                style="background: transparent; border: none;">
                <img src="../iconos/edit.png" alt="Editar" style="width: 20px; height: 20px;">
              </button>
              <button class="btn btn-danger btn-sm" onclick="deleteUsuario(${item.idUsuarios})" style="background: transparent; border: none;">
                <img src="../iconos/delete.png" alt="Eliminar" style="width: 20px; height: 20px;">
              </button>
              ${iconoStatus}
            `,
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
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
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
    dom: "iptlr",
    searching: true,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    pageLength: 10,
  });

  // Filtros
  $('input[name="statusType"]').on("change", function () {
    table.ajax.reload();
  });

  $("#searchText").on("keyup", function () {
    table.search(this.value).draw();
  });

  $("#resetFiltersBtn").on("click", function () {
    $("#searchText").val("");
    $('input[name="statusType"]').prop("checked", false);
    table.search("").column(5).search("").draw();
  });

  // Abrir modal y cargar datos
  $("#usuariosTable").on(
    "click",
    'button[data-bs-target="#modalEditUsuarios"]',
    function () {
      var usuarioData = {
        idUsuarios: $(this).data("id"),
        nombre_usuario: $(this).data("nombre"),
        correo_usuario: $(this).data("correo"),
        puesto_usuario: $(this).data("puesto"),
        perfil_usuario: $(this).data("perfil"),
        telefono_usuario: $(this).data("telefono"),
        status: $(this).data("status"),
        proyecto: $(this).data("proyecto"), // id_proyecto
      };

      cargarDatosUsuario(usuarioData);
    }
  );
});

function cargarDatosUsuario(usuarioData) {
  const proyectoSelect = document.querySelector("#edit_proyecto_usuario");

  document.querySelector("#accion").value = "3";
  document.querySelector("#edit_id_usuario").value = usuarioData.idUsuarios;
  document.querySelector("#edit_nombre_usuario").value =
    usuarioData.nombre_usuario || "";
  document.querySelector("#edit_correo_usuario").value =
    usuarioData.correo_usuario || "";
  document.querySelector("#edit_puesto_usuario").value =
    usuarioData.puesto_usuario || "";
  document.querySelector("#edit_telefono_usuario").value =
    usuarioData.telefono_usuario || "";
  document.querySelector("#edit_perfil_usuario").value =
    usuarioData.perfil_usuario || "";
  document.querySelector("#edit_status").value = usuarioData.status || "1";

  document.querySelector("#edit_proyecto_usuario").value =
    usuarioData.proyecto || "";
}

// Función para eliminar un usuario
function deleteUsuario(id) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¿Quieres eliminar este usuario?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Eliminando...",
        text: "Por favor espera",
        icon: "info",
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });
      window.location.href = `../Controllers/usuarios.php?accion=4&id=${id}`;
    }
  });
}
// // Función para activar/desactivar un usuario
// function toggleStatus(id, imgElement, status) {
//   // Preguntar al usuario si está seguro de cambiar el estado
//   Swal.fire({
//     title: "¿Estás seguro?",
//     text:
//       status === "1"
//         ? "¿Estás seguro que deseas deshabilitar este usuario?"
//         : "¿Estás seguro que deseas habilitar este usuario?",
//     icon: "info", // Cambiado a 'info' para usar el icono de info
//     showCancelButton: true,
//     confirmButtonText: "Confirmar",
//     cancelButtonText: "Cancelar",
//     confirmButtonColor: "#4B4A4B", // Color del botón Confirmar
//     cancelButtonColor: "#4B4A4B", // Color del botón Cancelar
//     customClass: {
//       confirmButton: "swal2-bold-button", // Clase personalizada para el texto en negrita
//       cancelButton: "swal2-bold-button", // Clase personalizada para el texto en negrita
//     },
//   }).then((result) => {
//     if (result.isConfirmed) {
//       var newStatus = status === "1" ? 0 : 1;
//       imgElement.setAttribute("data-status", newStatus);
//       imgElement.src =
//         // Mostrar carga mientras se actualiza el estado
//         Swal.fire({
//           title: "Actualizando estado...",
//           text: "Por favor espera",
//           icon: "info",
//           showConfirmButton: false,
//           allowOutsideClick: false,
//           didOpen: () => {
//             Swal.showLoading();
//           },
//         });

//       // Realiza la actualización en el backend (sin promesas, directamente)
//       window.location.href = `../Controllers/usuarios.php?accion=5&id=${id}&status=${status}`;
//     }
//   });

//   // Asegúrate de incluir una regla CSS para el estilo de los botones
//   document.head.insertAdjacentHTML(
//     "beforeend",
//     `
//         <style>
//             .swal2-bold-button {
//                 font-weight: bold;
//                 color: white !important;
//             }
//         </style>
//     `
//   );
// }
