$(document).ready(function() {
    var table = $('#table-proyectos').DataTable({
        "ajax": {
            "url": "../Controllers/catProyectos.php",  // Cambiar a la URL del controlador para proyectos
            "method": "POST",  
            "data": {
                "accion": 2  // Acción para obtener los datos de proyectos
            },
            "dataSrc": function (json) {
                var rows = [];
                $.each(json, function (index, item) {
                    rows.push([  
                        item.id,  
                        item.nombre_proyecto,  // Cambiar a nombre_proyecto
                        `  
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarProyecto" data-id="${item.id}" data-nombre="${item.nombre_proyecto}" data-status="${item.status}" style="background: transparent; border: none;">
                                <img src="../iconos/edit.png" alt="Editar" style="width: 20px; height: 20px;">
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProyecto(${item.id})" style="background: transparent; border: none;">
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
    $('#table-proyectos').on('click', 'button[data-bs-target="#modalEditarProyecto"]', function() {
        var proyectoData = {
            id: $(this).data('id'),
            nombre_proyecto: $(this).data('nombre'),
            status: $(this).data('status')
        };

        // Cargar los datos en el formulario de edición
        cargarDatosProyecto(proyectoData);
    });

    // Cargar los datos en el modal de edición
    function cargarDatosProyecto(proyectoData) {
        // Asignar valores a los campos del modal
        $('#edit_id_proyecto').val(proyectoData.id);
        $('#edit_nombre_proyecto').val(proyectoData.nombre_proyecto || ''); // Si no hay nombre, dejar vacío
       
    }
});

// Función para eliminar un proyecto
function deleteProyecto(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres eliminar este proyecto?',
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
                window.location.href = `../Controllers/catProyectos.php?accion=4&id=${id}`;  // Redirige para eliminar el proyecto
            }, 1500); // Redirigir después de que se muestre el mensaje de carga
        }
    });
}

// Función para activar/desactivar un proyecto
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
        window.location.href = `../Controllers/catProyectos.php?accion=5&id=${id}&status=${status}`;  // Redirige para actualizar el estado
    });
}
