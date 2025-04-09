$(document).ready(function() {
    var table = $('#actividadTable').DataTable({
        "order": [[0, "desc"]], // 0 es la primera columna (Fecha), "desc" para descendente
        "ajax": {
            "url": "../Controllers/actividad.php",  
            "method": "POST",  
            "data": function(d) {
                // Aquí se agrega el filtro de proyecto
                d.accion = 1;
            },
            "dataSrc": function (json) {
                var rows = [];
                $.each(json, function (index, item) {

                    rows.push([  
                        item.fecha,
                        item.user_id,
                        item.name_user,
                        item.description
                    ]);

                });
                return rows;
            }
        },
        "processing": true, 
        "language": {
            "processing": "<div class='loading-overlay'><div class='loader'></div></div>",  
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "loadingRecords": "Cargando...",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "No hay datos disponibles en la tabla",
            "paginate": {
                "first": "Primero",
                "previous": "Anterior",
                "next": "Siguiente",
                "last": "Último"
            }
        },
        "dom": 'iptlr',
        "searching": true,
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Todos"] ],
        "pageLength": 10 
    });

    // Filtro personalizado para DataTables
    $.fn.dataTable.ext.search.push(function(settings, data) {
        var startDate    = $('#startDate').val();
        var endDate      = $('#endDate').val();
        
        // Fecha de la fila en formato "dd-mm-yyyy HH:MM"
        var rowDateParts = data[0].split(" "); // Columna 0 (Fecha)
        var rowDate      = rowDateParts[0]; // "dd-mm-yyyy"
        var rowTime      = rowDateParts[1] ? rowDateParts[1] : "00:00"; // "HH:MM" o default

        // Convertir a formato Date JS (yyyy, mm-1, dd, HH, MM)
        var dateParts = rowDate.split("-");
        var formattedDate = new Date(
            dateParts[2], dateParts[1] - 1, dateParts[0],
            rowTime.split(":")[0], rowTime.split(":")[1]
        );

        var start = startDate ? new Date(startDate + "T00:00:00") : null;
        var end   = endDate ? new Date(endDate + "T23:59:59") : null;

        // Lógica de filtro por rango
        if ((!start || formattedDate >= start) && (!end || formattedDate <= end)) {
            return true;
        }
        return false;
    });

    // Detectar cambio en las fechas para redibujar la tabla
    $('#startDate, #endDate').on('change', function() {
        table.draw();
    });
  

    // Filtrar por texto de búsqueda
    $('#searchText').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Restablecer filtros
    $('#resetFiltersBtn').on('click', function() {
        //$('#filterDate').val('');
        $('#endDate').val('');
        $('#startDate').val('');
        $('#searchText').val('');
        table.search('').column(0).search('').column(1).search('').column(2).search('').draw();  // Limpia todos los filtros
    });

});