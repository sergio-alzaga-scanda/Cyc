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
              <button class="btn btn-warning btn-sm" data-id="${item.id_cyc}" style="background: transparent; border: none;">
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

  // Filtros de fecha
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

  // Filtro por texto
  $("#searchText").on("keyup", function () {
    table.search(this.value).draw();
  });

  // Filtros por tipo y status
  $('input[name="contingencyType"], input[name="statusType"]').on(
    "change",
    function () {
      var filterValue = this.value;
      var columnIndex = 4; // columna tipo
      if (filterValue === "ambos") {
        table.column(columnIndex).search("").draw();
      } else {
        table.column(columnIndex).search(filterValue).draw();
      }
    }
  );

  // Reset filtros
  $("#resetFiltersBtn").on("click", function () {
    $("#endDate").val("");
    $("#startDate").val("");
    $("#searchText").val("");
    $("#proyecto").val("");
    $('input[name="contingencyType"], input[name="statusType"]').prop(
      "checked",
      false
    );
    table.search("").columns().search("").draw();
  });

  // ================= MODAL DE EDICIÓN =================
  // Mostrar splash
  function mostrarSplash() {
    $("#splash").fadeIn();
  }
  function ocultarSplash() {
    $("#splash").fadeOut();
  }

  // Actualizar ubicaciones según proyecto
  function actualizarUbicaciones(proyectoId, ubicacionSeleccionada = null) {
    $("#ubicacion_edit option").each(function () {
      var proyecto = $(this).data("proyecto");
      if (!proyecto) return;
      $(this).toggle(proyecto == proyectoId);
    });
    if (ubicacionSeleccionada) {
      $("#ubicacion_edit").val(ubicacionSeleccionada);
    } else {
      $("#ubicacion_edit").val("");
    }
  }

  // Cargar datos en modal
  function cargarDatosModal(crisisData) {
    $("#id").val(crisisData.id_cyc);
    $("#no_ticket_edit").val(crisisData.no_ticket);
    $("#nombre_edit").val(crisisData.nombre);
    $("#categoria_edit").val(crisisData.categoria_cyc);
    $("#tipo_edit").val(crisisData.tipo_cyc);
    $("#edit_proyecto").val(crisisData.proyecto);
    $("#ivr_edit").val(crisisData.redaccion_cyc || "");

    // Checkbox y fecha
    if (crisisData.fecha_programacion) {
      $("#programar_edit").prop("checked", true);
      var fechaInput = crisisData.fecha_programacion
        .slice(0, 16)
        .replace(" ", "T");
      $("#fecha_programacion_2").val(fechaInput);
      $("#fecha-bloque-edit").show();
    } else {
      $("#programar_edit").prop("checked", false);
      $("#fecha_programacion_2").val("");
      $("#fecha-bloque-edit").hide();
    }

    // Ubicaciones
    actualizarUbicaciones(crisisData.proyecto, crisisData.ubicacion_cyc);

    // Canales y bots
    const canalesSeleccionados = crisisData.canal_cyc || [];
    $('[name="canal[]"]').each((i, el) => {
      $(el).prop("checked", canalesSeleccionados.includes($(el).val()));
    });
    const botsSeleccionados = crisisData.bot_cyc || [];
    $('[name="bot[]"]').each((i, el) => {
      $(el).prop("checked", botsSeleccionados.includes($(el).val()));
    });

    $("#editModal").modal("show");
  }

  // Evento click en editar
  $(document).on("click", ".btn-warning", function () {
    var crisisId = $(this).data("id");
    mostrarSplash();
    $.ajax({
      url: "../Controllers/crisis.php",
      method: "POST",
      dataType: "json",
      data: { accion: 3, id: crisisId },
      success: function (data) {
        cargarDatosModal(data);
        ocultarSplash();
      },
      error: function () {
        alert("Error al cargar los datos de la crisis.");
        ocultarSplash();
      },
    });
  });

  // Cambio de proyecto en modal
  $("#edit_proyecto").on("change", function () {
    actualizarUbicaciones($(this).val());
  });

  // Checkbox programar
  $("#programar_edit").on("change", function () {
    if ($(this).is(":checked")) {
      $("#fecha-bloque-edit").show();
    } else {
      $("#fecha-bloque-edit").hide();
      $("#fecha_programacion_2").val("");
    }
  });

  // Inicialización
  if (!$("#programar_edit").is(":checked")) {
    $("#fecha-bloque-edit").hide();
  }
});
