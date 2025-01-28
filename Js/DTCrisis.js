$(document).ready(function() {
    var table = $('#crisisTable').DataTable({
        "ajax": {
            "url": "../Controllers/crisis.php",  
            "method": "POST",  
            "data": function(d) {
                // Aquí se agrega el filtro de proyecto
                d.accion = 2;
                d.proyecto = $('#proyecto').val();  // Agrega el valor seleccionado del proyecto
            },
            "dataSrc": function (json) {
                var rows = [];
                $.each(json, function (index, item) {
                    var iconoStatus = item.status_cyc === '1' ? 
                        `<img src="../iconos/activo.png" alt="Activo" style="width: 50px; height: 25px;" data-status="1" data-id="${item.id_cyc}" onclick="toggleStatus(${item.id_cyc}, this, ${item.status_cyc})">` : 
                        `<img src="../iconos/desactivo.png" alt="Desactivado" style="width: 50px; height: 25px;" data-status="0" data-id="${item.id_cyc}" onclick="toggleStatus(${item.id_cyc}, this, ${item.status_cyc})">`;

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
                        `
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

    

    // Filtrar por fecha
    $('#filterDate').on('change', function() {
        var selectedDate = $(this).val();
        var formattedDate = selectedDate.split('-').reverse().join('-');
        table.column(5).search(formattedDate).draw();
    });

    // Filtrar por texto de búsqueda
    $('#searchText').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Filtrar por tipo de contingencia
    $('input[name="contingencyType"]').on('change', function() {
        var filterValue = this.value;
        if (filterValue === "ambos") {
            table.column(4).search('').draw();  // Limpiar el filtro de tipo
        } else {
            table.column(4).search(filterValue).draw();
        }
    });

    // Restablecer filtros
    $('#resetFiltersBtn').on('click', function() {
        $('#filterDate').val('');
        $('#searchText').val('');
        $('#proyecto').val('');  // Limpiar el filtro de proyecto
        $('input[name="contingencyType"]').prop('checked', false);
        table.search('').column(5).search('').column(3).search('').column(6).search('').draw();  // Limpia todos los filtros
    });
});

// Función para cargar los datos en el formulario de edición
function cargarDatosCrisis(crisisData) {
    document.querySelector('#no_ticket_edit').value = crisisData.no_ticket || '';
    document.querySelector('#nombre_edit').value = crisisData.nombre || '';
    document.querySelector('#ubicacion_edit').value = crisisData.ubicacion_cyc || '';
    document.querySelector('#ivr_edit').value = crisisData.redaccion_cyc || '';
    document.querySelector('#redaccion_canales_edit').value = crisisData.redaccion_canales || '';
    document.querySelector('#proyecto').value = crisisData.proyecto || '';

    const checkboxProgramas = document.querySelector('#programar_edit');
    if (crisisData.fecha_programacion) {
        checkboxProgramas.checked = true;
        document.querySelector('#fecha_programacion_edit').value = crisisData.fecha_programacion;
    } else {
        checkboxProgramas.checked = false;
        document.querySelector('#fecha_programacion_edit').value = '';
    }

    const canalesSeleccionados = crisisData.canal_cyc || [];
    document.querySelectorAll('[name="canal[]"]').forEach((checkbox) => {
        if (canalesSeleccionados.includes(checkbox.value)) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    });

    const botsSeleccionados = crisisData.bot_cyc || [];
    document.querySelectorAll('[name="bot[]"]').forEach((checkbox) => {
        if (botsSeleccionados.includes(checkbox.value)) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    });
}

// Función para eliminar una crisis
function deleteCrisis(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres eliminar esta crisis?',
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

            window.location.href = `../Controllers/crisis.php?accion=4&id=${id}`;
        }
    });
}

// Función para activar/desactivar una crisis
function toggleStatus(id, imgElement, status_cyc) {
    // Si el estatus es '1' (activo), desactivamos; de lo contrario, activamos.
    var status = (status_cyc === '1') ? 0 : 1;  // 1 = activar, cualquier otro valor = desactivar
    // Actualiza el ícono visualmente
    imgElement.setAttribute('data-status', status);
    imgElement.src = (status === 1) ? "../iconos/activo.png" : "../iconos/desactivo.png";
    
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

    // Realiza la actualización en el backend (sin promesas, directamente)
    window.location.href = `../Controllers/crisis.php?accion=6&id=${id}&status=${status_cyc}`;
}
