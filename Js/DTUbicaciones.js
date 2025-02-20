$(document).ready(function() {
    var table = $('#table-ubicaciones').DataTable({
        "ajax": {
            "url": "../Controllers/catUbicaciones.php",  
            "method": "POST",  
            "data": {
                "accion": 2  // Acción para obtener los datos de ubicación IVR
            },
            "dataSrc": function (json) {
                var rows = [];
                $.each(json, function (index, item) {
                    // Cambiar el icono de estado dependiendo del valor de "status"
                    var iconoStatus = item.status === 1 ? 
                        `<img src="../iconos/activo.png" alt="Activo" style="width: 50px; height: 25px;" data-status="1" data-id="${item.id}" onclick="toggleStatus(${item.id}, this, ${item.status})">` : 
                        `<img src="../iconos/desactivo.png" alt="Desactivado" style="width: 50px; height: 25px;" data-status="0" data-id="${item.id}" onclick="toggleStatus(${item.id}, this, ${item.status})">`;

                    // Crear las filas de la tabla
                    rows.push([  
                        item.id,  // ID de la ubicación IVR
                        item.nombre_ubicacion_ivr,  // Nombre de la ubicación IVR
                        `  
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarUbicacionIVR" data-id="${item.id}" data-nombre="${item.nombre_ubicacion_ivr}" data-status="${item.status}" style="background: transparent; border: none;">
                                <img src="../iconos/edit.png" alt="Editar" style="width: 20px; height: 20px;">
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteUbicacionIVR(${item.id})" style="background: transparent; border: none;">
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
    $('#table-ubicaciones').on('click', 'button[data-bs-target="#modalEditarUbicacionIVR"]', function() {
        var ubicacionData = {
            id: $(this).data('id'),
            nombre_ubicacion_ivr: $(this).data('nombre'),
            status: $(this).data('status')
        };

        // Cargar los datos en el formulario de edición
        cargarDatosUbicacionIVR(ubicacionData);
    });

    // Cargar los datos en el modal de edición
    function cargarDatosUbicacionIVR(ubicacionData) {
        // Asignar valores a los campos del modal
        $('#edit_id_ubicacion_ivr').val(ubicacionData.id);
        $('#edit_nombre_ubicacion_ivr').val(ubicacionData.nombre_ubicacion_ivr || ''); // Si no hay nombre, dejar vacío
        $('#accion_editar').val('3');  // Cambiar la acción a 'editar'
    }
});

// Función para eliminar una ubicación IVR
function deleteUbicacionIVR(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres eliminar esta ubicación IVR?',
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
                window.location.href = `../Controllers/catUbicaciones.php?accion=4&id=${id}`;  // Redirige para eliminar la ubicación IVR
            }, 1500); // Redirigir después de que se muestre el mensaje de carga
        }
    });
}

// Función para activar/desactivar una ubicación IVR
function toggleStatus(id, imgElement, status) {
    // Determinar el nuevo estado
    var newStatus = (status === 1) ? 0 : 1; // Si está activo (1), lo cambiamos a desactivado (0) y viceversa

    // Cambiar el atributo de datos y la imagen de estado
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

    // Enviar la solicitud AJAX para cambiar el estado
    $.ajax({
        url: '../Controllers/catUbicaciones.php', 
        method: 'GET', 
        data: {
            accion: 5,  // Acción para cambiar el estado
            id: id,     // ID de la ubicación IVR
            status: newStatus  // Nuevo estado (activo o desactivado)
        },
        success: function(response) {
            Swal.close();  // Cerrar el loader después de la respuesta
            // Aquí puedes agregar código para manejar la respuesta de la actualización, si es necesario
        },
        error: function() {
            Swal.fire('Error', 'Ocurrió un error al actualizar el estado.', 'error');
        }
    });
}
