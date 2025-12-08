$(document).ready(function () {
  var table = $("#crisisTable").DataTable({
    ajax: {
      url: "../Controllers/crisis.php",
      method: "POST",
      data: function (d) {
        d.accion = 2;
        d.proyecto = $("#proyecto").val();
      },
      dataSrc: function (json) {
        var rows = [];
        $.each(json, function (index, item) {
          var status = item.status_cyc;
          var iconoStatus =
            status === 1
              ? `<img src="../iconos/activo.png" alt="Activo" style="width: 50px; height: 25px;" data-status="1" data-id="${item.id_cyc}" onclick="toggleStatus(${item.id_cyc}, this, 1)">`
              : `<img src="../iconos/desactivo.png" alt="Desactivado" style="width: 50px; height: 25px;" data-status="0" data-id="${item.id_cyc}" onclick="toggleStatus(${item.id_cyc}, this, 0)">`;

          rows.push([
            item.id_cyc,
            item.no_ticket,
            item.nombre_proyecto,
            item.categoria_nombre,
            item.tipo_cyc,
            item.nombre_ubicacion,
            item.fecha_activacion,
            `
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" data-id="${item.id_cyc}" style="background: transparent; border: none;">
                <img src="../iconos/edit.png" alt="Editar" style="width: 20px; height: 20px;">
              </button>
              <button class="btn btn-danger btn-sm" onclick="deleteCrisis(${item.id_cyc})" style="background: transparent; border: none;">
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
    },
    dom: "iptlr",
    searching: true,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    pageLength: 10,
  });

  $("#startDate, #endDate").on("change", function () {
    var startDate = $("#startDate").val();
    var endDate = $("#endDate").val();

    if (startDate && endDate) {
      var start = new Date(startDate + "T00:00:00");
      var end = new Date(endDate + "T23:59:59");

      table
        .column(6)
        .search(function (data) {
          var rowDateParts = data.split(" ");
          var rowDate = rowDateParts[0];
          var rowTime = rowDateParts[1];
          var dateParts = rowDate.split("-");
          var formattedDate = new Date(
            dateParts[2],
            dateParts[1] - 1,
            dateParts[0],
            rowTime.split(":")[0],
            rowTime.split(":")[1]
          );
          return formattedDate >= start && formattedDate <= end;
        })
        .draw();
    } else {
      table.column(6).search("").draw();
    }
  });

  $("#searchText").on("keyup", function () {
    table.search(this.value).draw();
  });

  $('input[name="contingencyType"]').on("change", function () {
    var filterValue = this.value;
    if (filterValue === "ambos") {
      table.column(4).search("").draw();
    } else {
      table.column(4).search(filterValue).draw();
    }
  });

  $('input[name="statusType"]').on("change", function () {
    var filterValue = this.value;
    if (filterValue === "ambos") {
      table.column(4).search("").draw();
    } else {
      table.column(4).search(filterValue).draw();
    }
  });

  $("#resetFiltersBtn").on("click", function () {
    $("#endDate").val("");
    $("#startDate").val("");
    $("#searchText").val("");
    $("#proyecto").val("");
    $('input[name="contingencyType"]').prop("checked", false);
    table.search("").columns().search("").draw();
  });
});

function cargarDatosCrisis(crisisData) {
  document.querySelector("#no_ticket_edit").value = crisisData.no_ticket || "";
  document.querySelector("#nombre_edit").value = crisisData.nombre || "";
  document.querySelector("#ubicacion_edit").value =
    crisisData.ubicacion_cyc || "";
  document.querySelector("#ivr_edit").value = crisisData.redaccion_cyc || "";
  document.querySelector("#redaccion_canales_edit").value =
    crisisData.redaccion_canales || "";
  document.querySelector("#proyecto").value = crisisData.proyecto || "";

  const checkboxProgramas = document.querySelector("#programar_edit");
  if (crisisData.fecha_programacion) {
    checkboxProgramas.checked = true;
    document.querySelector("#fecha_programacion_edit").value =
      crisisData.fecha_programacion;
  } else {
    checkboxProgramas.checked = false;
    document.querySelector("#fecha_programacion_edit").value = "";
  }

  const canalesSeleccionados = crisisData.canal_cyc || [];
  document.querySelectorAll('[name="canal[]"]').forEach((checkbox) => {
    checkbox.checked = canalesSeleccionados.includes(checkbox.value);
  });

  const botsSeleccionados = crisisData.bot_cyc || [];
  document.querySelectorAll('[name="bot[]"]').forEach((checkbox) => {
    checkbox.checked = botsSeleccionados.includes(checkbox.value);
  });
}

function deleteCyc(id) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Esta acción eliminará este registro (borrado lógico).",
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
        didOpen: () => Swal.showLoading(),
      });

      setTimeout(() => {
        window.location.href = `../Controllers/cyc.php?accion=5&id=${id}`;
      }, 800);
    }
  });
}

function toggleStatus(id, imgElement, status_cyc) {
  status_cyc = Number(status_cyc);

  // Lógica para cambiar el status
  let nuevoStatus;
  if (status_cyc === 1) {
    // Si está activo, cambiar a 2 (deshabilitado parcial o estado intermedio)
    nuevoStatus = 2;
  } else if (status_cyc > 1) {
    // Si es mayor a 1, volver a 1 (activo)
    nuevoStatus = 1;
  } else {
    nuevoStatus = status_cyc; // otros valores no cambian
  }

  // Mensaje dinámico según el estado actual
  const mensaje =
    status_cyc === 1
      ? "¿Estás seguro que deseas deshabilitar la grabación? Esto será eliminado inmediatamente de Five9"
      : "¿Estás seguro que deseas habilitar la grabación? Esto será publicado inmediatamente en Five9";

  Swal.fire({
    title: "¿Estás seguro?",
    text: mensaje,
    icon: "info",
    showCancelButton: true,
    confirmButtonText: "Confirmar",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#4B4A4B",
    cancelButtonColor: "#4B4A4B",
    customClass: {
      confirmButton: "swal2-bold-button",
      cancelButton: "swal2-bold-button",
    },
    didOpen: () => {
      document.querySelector(
        ".swal2-confirm"
      ).innerHTML = `Confirmar <img src="../iconos/Group-4.svg" alt="info icon" style="width: 20px; height: 20px; margin-left: 8px;">`;
      document.querySelector(
        ".swal2-cancel"
      ).innerHTML = `Cancelar <img src="../iconos/cancelar.png" alt="cancel icon" style="width: 20px; height: 20px; margin-left: 8px;">`;
    },
  }).then((result) => {
    if (result.isConfirmed) {
      // Actualizamos el atributo data-status y el icono visual
      imgElement.setAttribute("data-status", nuevoStatus);

      // Cambiamos el icono según el nuevo estado
      if (nuevoStatus === 1) {
        imgElement.src = "../iconos/activo.png";
        imgElement.alt = "Activo";
      } else {
        // Cualquier estado mayor a 1
        imgElement.src = "../iconos/desactivo.png"; // puedes poner otro icono si quieres diferenciar
        imgElement.alt = "Desactivado";
      }

      Swal.fire({
        title: "Actualizando estado...",
        text: "Por favor espera",
        icon: "info",
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      // Llamada al PHP para actualizar en la base de datos
      window.location.href = `../Controllers/crisis.php?accion=6&id=${id}`;
    }
  });

  // Agregar estilo solo si no existe
  if (!document.querySelector("style#swal-custom-style")) {
    document.head.insertAdjacentHTML(
      "beforeend",
      `
      <style id="swal-custom-style">
        .swal2-bold-button {
          font-weight: bold;
          color: white !important;
        }
      </style>
    `
    );
  }
}
