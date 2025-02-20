$(document).ready(function() {
    var table = $('#table-bots').DataTable({
        "ajax": {
            "url": "../Controllers/catBot.php",  // Cambiar a la URL del controlador para bots
            "method": "POST",  
            "data": {
                "accion": 2  // Acción para obtener los datos de bot
            },
            "dataSrc": function (json) {
                var rows = [];
                $.each(json, function (index, item) {
                   
                    rows.push([  
                        item.id,  
                        item.nombre_bot,  // Cambiar a nombre_bot
                        `  
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarBot" data-id="${item.id}" data-nombre="${item.nombre_bot}" data-status="${item.status}" style="background: transparent; border: none;">
                                <img src="../iconos/edit.png" alt="Editar" style="width: 20px; height: 20px;">
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteBot(${item.id})" style="background: transparent; border: none;">
                                <img src="../iconos/delete.png" alt="Eliminar" style="width: 20px; height: 20px;">
                            </button>
                           
                        ` // Fin de la columna de acciones
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
            },
            "aria": {
                "sortAscending": ": activar para ordenar la columna de manera ascendente",
                "sortDescending": ": activar para ordenar la columna de manera descendente"
            }
        },
        "dom": 'ptlr',
        "searching": true,
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Todos"] ],
        "pageLength": 10 
    });

    // Manejar el evento de clic en el botón de editar
    $('#table-bots').on('click', 'button[data-bs-target="#modalEditarBot"]', function() {
        var botData = {
            id: $(this).data('id'),
            nombre_bot: $(this).data('nombre'),
            status: $(this).data('status')
        };

        // Cargar los datos en el formulario de edición
        cargarDatosBot(botData);
    });

    // Cargar los datos en el modal de edición
    function cargarDatosBot(botData) {
        // Asignar valores a los campos del modal
        $('#edit_id_bot').val(botData.id);
        $('#edit_nombre_bot').val(botData.nombre_bot || ''); // Si no hay nombre, dejar vacío
        $('#accion_editar').val('3');  // Cambiar la acción a 'editar'
    }
});

// Función para eliminar un bot
function deleteBot(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres eliminar este bot?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar carga mientras se procesa la eliminación
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Redirigir a la acción de eliminación en el controlador
            setTimeout(() => {
                window.location.href = `../Controllers/catBot.php?accion=4&id=${id}`;  // Redirige para eliminar el bot
            }, 1500); // Redirigir después de que se muestre el mensaje de carga
        }
    });
}

// Función para activar/desactivar un bot
function toggleStatus(id, imgElement, status) {
    var newStatus = (status === '1') ? 1 : 2;
    imgElement.setAttribute('data-status', newStatus);
    imgElement.src = (newStatus === 1) ? "../iconos/activo.png" : "../iconos/desactivo.png";

    // Mostrar carga mientras se actualiza el estado
    Swal.fire({
        title: 'Actualizando estado...',
        text: 'Por favor espera',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Redirigir para cambiar el estado
    setTimeout(() => {
        window.location.href = `../Controllers/catBot.php?accion=5&id=${id}&status=${status}`;  // Redirige para actualizar el estado
    });
}
