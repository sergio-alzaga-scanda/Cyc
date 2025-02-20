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

    

$('#startDate, #endDate').on('change', function() {
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    // Asegurarnos de que ambas fechas están seleccionadas
    if (startDate && endDate) {
        // Convertir las fechas de inicio y fin a objetos Date con hora completa
        var start = new Date(startDate + "T00:00:00"); // Fecha de inicio con hora a las 00:00
        var end = new Date(endDate + "T23:59:59"); // Fecha de fin con hora a las 23:59:59

        console.log("Start Date: ", start);
        console.log("End Date: ", end);

        // Filtrar las filas en la columna de fecha según el rango
        table.column(6).search(function(data, rowIndex) {
            // El formato de la fecha en los datos es "dd-mm-yyyy HH:MM"
            var rowDateParts = data.split(" "); // Separar la fecha de la hora
            var rowDate = rowDateParts[0]; // Fecha en formato "dd-mm-yyyy"
            var rowTime = rowDateParts[1]; // Hora en formato "HH:MM"

            // Convertir "dd-mm-yyyy" a "yyyy-mm-dd" para crear un objeto Date
            var dateParts = rowDate.split("-"); // Separar en [dd, mm, yyyy]
            var formattedDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0], rowTime.split(":")[0], rowTime.split(":")[1]);

            console.log("Row Date: ", formattedDate);

            // Verificar si la fecha está dentro del rango
            return formattedDate >= start && formattedDate <= end;
        }).draw();
    } else {
        // Si no hay un rango de fechas válido, mostrar todas las filas
        table.column(6).search('').draw();
    }
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

    // Filtrar por tipo de Estado
    $('input[name="statusType"]').on('change', function() {
        var filterValue = this.value;
        if (filterValue === "ambos") {
            table.column(4).search('').draw();  // Limpiar el filtro de tipo
        } else {
            table.column(4).search(filterValue).draw();
        }
    });

    // Restablecer filtros
    $('#resetFiltersBtn').on('click', function() {
        //$('#filterDate').val('');
        $('#endDate').val('');
        $('#startDate').val('');
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
function toggleStatus(id, imgElement, status_cyc) {
    // Preguntar al usuario si está seguro de cambiar el estado
    Swal.fire({
        title: '¿Estás seguro?',
        text: (status_cyc === '1') ? '¿Estás seguro que deseas deshabilitar la grabación? Esto será eliminado inmediatamente de Five9' 
        : '¿Estás seguro que deseas habilitar la grabación? Esto será publicado inmediatamente en Five9',
        icon: 'info', // Cambiado a 'info' para usar el icono de info
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#4B4A4B', // Color del botón Confirmar
        cancelButtonColor: '#4B4A4B', // Color del botón Cancelar
        customClass: {
            confirmButton: 'swal2-bold-button', // Clase personalizada para el texto en negrita
            cancelButton: 'swal2-bold-button' // Clase personalizada para el texto en negrita
        },
        didOpen: () => {
            // Cambiar icono de confirmación y cancelación, el texto va primero
            document.querySelector('.swal2-confirm').innerHTML = `Confirmar <img src="../iconos/Group-4.svg" alt="info icon" style="width: 20px; height: 20px; margin-left: 8px;">`;
            document.querySelector('.swal2-cancel').innerHTML = `Cancelar <img src="../iconos/cancelar.png" alt="cancel icon" style="width: 20px; height: 20px; margin-left: 8px;">`;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Si el usuario confirma, actualizamos el estado
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
    });

    // Asegúrate de incluir una regla CSS para el estilo de los botones
    document.head.insertAdjacentHTML('beforeend', `
        <style>
            .swal2-bold-button {
                font-weight: bold;
                color: white !important;
            }
        </style>
    `);
}
