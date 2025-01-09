<!-- Cargar jQuery primero -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Cargar DataTables después de jQuery -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<!-- Cargar otros scripts que dependen de jQuery y DataTables -->
<script>
    $(document).ready(function () {
        tabla_Coord = $('#TblCoord').DataTable({
            "dom": 'Bfrtip',
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No hay registros",
                "loading": "Cargando",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "",
                "sSearch": "🔎",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Sig.",
                    "sPrevious": "Ant."
                },
            },
            "buttons": [
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf"></i>',
                    className: 'btn btn-danger',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel"></i>',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
            ],
            "order": [],
            "ajax": {
                "url": "../Controllers/crisis.php",
                "method": 'POST',
                "data": {
                    accion: 2, //enviamos opción 1 para que haga un SELECT
                    cliente: <?php echo $cliente; ?>,
                    fechaInicial: '<?php echo $fecha_convertidaI; ?>',
                    fechaFinal: '<?php echo $fecha_convertidaF; ?>'
                },
                "dataSrc": function (json) {
                    var data = json.map(function (item) {
                        return {
                            'id': item.id_cyc,
                            'nombreCompleto': item.nombre,
                            'no_ticket': item.no_ticket,
                            'categoria_cyc': item.categoria_cyc,
                            'tipo_cyc': item.tipo_cyc,
                            'ubicacion_cyc': item.ubicacion_cyc,
                            'redaccion_cyc': item.redaccion_cyc,
                            'canal_cyc': item.canal_cyc,
                            'bot_cyc': item.bot_cyc,
                            'redaccion_canal_cyc': item.redaccion_canal_cyc,
                            'fecha_registro_cyc': item.fecha_registro_cyc,
                            'status_cyc': item.status_cyc,
                            'fecha_programacion': item.fecha_programacion,
                            'id_usuario': item.id_usuario
                        };
                    });
                    return data;
                }
            },
            "columns": [
                { "data": 'id' },
                { "data": 'nombreCompleto' },
                { "data": 'categoria_cyc' },
                { "data": 'tipo_cyc' },
                { "data": 'ubicacion_cyc' },
                { "data": 'status_cyc' },
                { "data": 'fecha_registro_cyc' },
                {
                    "data": 'activo',
                    "render": function (data, type, row) {
                        if (data == 9) {
                            return "<button type='button' class='p-1 btn btn-primary btn-sm mt-2 fs-6 Autorizacion' data-cod_autorizar=5>Ver detalles </button><br><button type='button' class='p-1 btn btn-dark btn-sm mt-2 fs-6 Autorizacion' data-cod_autorizar=6> Historico </button>";
                        }
                        else {
                            return "<button type='button' class='p-1 btn btn-primary btn-sm mt-2 fs-6 Autorizacion' data-cod_autorizar=5>Ver detalles </button><br>";
                        }
                    },
                },
            ],
        });

    });
</script>
