$(document).ready(function() {
    var table = $('#usuariosTable').DataTable({
        "ajax": {
            "url": "../Controllers/usuarios.php",  
            "method": "POST",  
            "data": {
                "accion": 2  
            },
            "dataSrc": function (json) {
                var rows = [];
                $.each(json, function (index, item) {
                    var iconoStatus = item.status === '1' ? 
                        `<img src="../iconos/activo.png" alt="Activo" style="width: 50px; height: 25px;" data-status="1" data-id="${item.idUsuarios}" onclick="toggleStatus(${item.idUsuarios}, this, ${item.status})">` : 
                        `<img src="../iconos/desactivo.png" alt="Desactivado" style="width: 50px; height: 25px;" data-status="0" data-id="${item.idUsuarios}" onclick="toggleStatus(${item.idUsuarios}, this, ${item.status})">`;

                    rows.push([  
                        item.idUsuarios,
                        item.nombre_usuario,
                        item.correo_usuario,  
                        item.puesto_usuario,  
                        item.nombre_perfil,
                        `  
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditUsuarios" data-id="${item.idUsuarios}" data-nombre="${item.nombre_usuario}" data-correo="${item.correo_usuario}" data-puesto="${item.puesto_usuario}" data-perfil="${item.perfil_usuario}" data-telefono="${item.telefono_usuario}" data-status="${item.status}" style="background: transparent; border: none;">
                                <img src="../iconos/edit.png" alt="Editar" style="width: 20px; height: 20px;">
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteUsuario(${item.idUsuarios})" style="background: transparent; border: none;">
                                <img src="../iconos/delete.png" alt="Eliminar" style="width: 20px; height: 20px;">
                            </button>
                            ${iconoStatus}
                        `
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
            },
            "aria": {
                "sortAscending": ": activar para ordenar la columna de manera ascendente",
                "sortDescending": ": activar para ordenar la columna de manera descendente"
            }
        },
        "dom": 'iptlr',
        "searching": true,
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Todos"] ],
        "pageLength": 10 
    });

    // Filtros
    $('input[name="statusType"]').on('change', function() {
        table.ajax.reload(); 
    });

    // Filtrar por texto de búsqueda
    $('#searchText').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Restablecer filtros
    $('#resetFiltersBtn').on('click', function() {
        $('#searchText').val('');
        $('input[name="statusType"]').prop('checked', false);
        table.search('').column(5).search('').draw();  // Limpia todos los filtros
    });

    // Manejar el evento de clic en el botón de editar
    $('#usuariosTable').on('click', 'button[data-bs-target="#modalEditUsuarios"]', function() {
        var usuarioData = {
            idUsuarios: $(this).data('id'),
            nombre_usuario: $(this).data('nombre'),
            correo_usuario: $(this).data('correo'),
            puesto_usuario: $(this).data('puesto'),
            perfil_usuario: $(this).data('perfil'),
            telefono_usuario: $(this).data('telefono'),
            status: $(this).data('status')
        };

        // Cargar los datos en el formulario de edición
        cargarDatosUsuario(usuarioData);
    });
});

// Función para cargar los datos en el formulario de edición
function cargarDatosUsuario(usuarioData) {
    document.querySelector('#accion').value = '3';  // Cambiar la acción a 'editar'
    document.querySelector('#edit_id_usuario').value = usuarioData.idUsuarios;  // Asignar el ID del usuario

    document.querySelector('#edit_nombre_usuario').value = usuarioData.nombre_usuario || '';
    document.querySelector('#edit_correo_usuario').value = usuarioData.correo_usuario || '';
    document.querySelector('#edit_puesto_usuario').value = usuarioData.puesto_usuario || '';
    document.querySelector('#edit_telefono_usuario').value = usuarioData.telefono_usuario || '';

    // Establecer el perfil del usuario
    document.querySelector('#edit_perfil_usuario').value = usuarioData.perfil_usuario || ''; 

    // Establecer el estado
    document.querySelector('#edit_status').value = usuarioData.status || '1';
}

// Función para eliminar un usuario
function deleteUsuario(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres eliminar este usuario?',
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

            window.location.href = `../Controllers/usuarios.php?accion=4&id=${id}`;
        }
    });
}

// Función para activar/desactivar un usuario
function toggleStatus(id, imgElement, status) {
    var newStatus = (status === '1') ? 0 : 1;
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

    window.location.href = `../Controllers/usuarios.php?accion=5&id=${id}&status=${status}`;
}
