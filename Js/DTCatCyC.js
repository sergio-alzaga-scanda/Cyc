$(document).ready(function() {
    var table = $('#tabla_cycs_data').DataTable({
        "ajax": {
            "url": "../Controllers/catCyC.php",  
            "method": "POST",  
            "data": {
                "accion": 2  // Acción para obtener los datos de crisis
            },
            "dataSrc": function (json) {
                var rows = [];
                $.each(json, function (index, item) {
                    // Solo mostrar los datos relevantes sin la opción de activar/desactivar
                    rows.push([  
                        item.id,  // ID de la crisis
                        item.nombre_crisis,  // Nombre de la crisis
                        item.criticidad,  // Criticidad
                        `  
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarCatCYC" data-id="${item.id}" data-nombre="${item.nombre_crisis}" data-criticidad="${item.criticidad}" data-status="${item.status}" style="background: transparent; border: none;">
                                <img src="../iconos/edit.png" alt="Editar" style="width: 20px; height: 20px;">
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCrisisCat(${item.id})" style="background: transparent; border: none;">
                                <img src="../iconos/delete.png" alt="Eliminar" style="width: 20px; height: 20px;">
                            </button>
                        ` // Ya no incluimos el icono de estado
                    ]);
                });
                return rows;
            }
        },
        "processing": true,  // Activa el procesamiento
        "language": {
            "processing": "<div class='loading-overlay'><div class='loader'></div></div>",  // Agrega un indicador de carga
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
    $('#tabla_cycs_data').on('click', 'button[data-bs-target="#modalEditarCatCYC"]', function() {
        var crisisData = {
            id: $(this).data('id'),
            nombre_crisis: $(this).data('nombre'),
            criticidad: $(this).data('criticidad'),
            status: $(this).data('status')
        };

        // Cargar los datos en el formulario de edición
        cargarDatosCrisis(crisisData);
    });
});

// Función para cargar los datos en el formulario de edición
function cargarDatosCrisis(crisisData) {
    document.querySelector('#accion').value = '3';  // Cambiar la acción a 'editar'
    document.querySelector('#edit_id').value = crisisData.id;  // Asignar el ID de la crisis
    document.querySelector('#edit_nombre').value = crisisData.nombre_crisis || '';
    document.querySelector('#edit_criticidad').value = crisisData.criticidad || '';
    document.querySelector('#edit_status').value = crisisData.status || '1';
}

// Función para eliminar una crisis
function deleteCrisisCat(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres eliminar esta categoría?',
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
                window.location.href = `../Controllers/catCyC.php?accion=4&id=${id}`;
            }, 1500); // Redirigir después de que se muestre el mensaje de carga
        }
    });
}
