$(document).ready(function() {
    var table = $('#table-canales').DataTable({
        "ajax": {
            "url": "../Controllers/catCanales.php",  
            "method": "POST",  
            "data": {
                "accion": 2  // Acción para obtener los datos de canal digital
            },
            "dataSrc": function (json) {
                var rows = [];
                $.each(json, function (index, item) {
                  
                    rows.push([  
                        item.id,  
                        item.nombre_canal,  
                        `  
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarCanal" data-id="${item.id}" data-nombre="${item.nombre_canal}" data-status="${item.status}" style="background: transparent; border: none;">
                                <img src="../iconos/edit.png" alt="Editar" style="width: 20px; height: 20px;">
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCrisisCat(${item.id})" style="background: transparent; border: none;">
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
    $('#table-canales').on('click', 'button[data-bs-target="#modalEditarCanal"]', function() {
        var canalData = {
            id: $(this).data('id'),
            nombre_canal: $(this).data('nombre'),
            status: $(this).data('status')
        };

        // Cargar los datos en el formulario de edición
        cargarDatosCanal(canalData);
    });

    // Cargar los datos en el modal de edición
    function cargarDatosCanal(canalData) {
        // Asignar valores a los campos del modal
        $('#edit_id_canal').val(canalData.id);
        $('#edit_nombre_canal').val(canalData.nombre_canal || ''); // Si no hay nombre, dejar vacío
        $('#accion_editar').val('3');  // Cambiar la acción a 'editar'
    }
});

// Función para eliminar un canal
function deleteCrisisCat(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres eliminar este canal?',
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
                window.location.href = `../Controllers/catCanales.php?accion=4&id=${id}`;  // Redirige para eliminar el canal
            }, 1500); // Redirigir después de que se muestre el mensaje de carga
        }
    });
}

// Función para activar/desactivar un canal
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
        window.location.href = `../Controllers/catCanales.php?accion=5&id=${id}&status=${status}`;  // Redirige para actualizar el estado
    });
}
