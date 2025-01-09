$(document).ready(function() {
    var table = $('#crisisTable').DataTable({
        "ajax": {
            "url": "../Controllers/crisis.php",  
            "method": "POST",  
            "data": {
                "accion": 2
            },
            "dataSrc": function (json) {
                var rows = [];
                $.each(json, function (index, item) {
                    var checked = item.status_cyc === '1' ? 'checked' : '';
                    rows.push([  
                        item.id_cyc,
                        item.nombre, 
                        item.no_ticket,
                        item.categoria_nombre,  
                        item.tipo_cyc,  
                        item.ubicacion_cyc,  
                        item.fecha_activacion,  
                        `  
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" data-id="${item.id_cyc}">Editar</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCrisis(${item.id_cyc})">Eliminar</button>
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked_${item.id_cyc}" ${checked} data-id="${item.id_cyc}" data-status="${item.status_cyc}" onclick="toggleCrisis(${item.id_cyc}, this)">
                        `
                    ]);
                });
                return rows;
            }
        },
        "columns": [
            { "title": "ID" },
            { "title": "Nombre" },
            { "title": "No. Ticket" },
            { "title": "Categoría" },
            { "title": "Tipo" },
            { "title": "Ubicación" },
            { "title": "Fecha Activación" },
            { "title": "Acciones" }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/Spanish.json"
        },
        "dom": 'lfrtip',  
    });

// Función para cargar los datos en el formulario de edición
function cargarDatosCrisis(crisisData) {
    // Llenar los campos del formulario
    document.querySelector('#no_ticket_edit').value = crisisData.no_ticket || '';
    document.querySelector('#nombre_edit').value = crisisData.nombre || '';
    document.querySelector('#ubicacion_edit').value = crisisData.ubicacion_cyc || '';
    document.querySelector('#ivr_edit').value = crisisData.redaccion_cyc || '';
    document.querySelector('#redaccion_canales_edit').value = crisisData.redaccion_canales || '';

    // Manejar la selección del checkbox "Programas"
    const checkboxProgramas = document.querySelector('#programar_edit');
    if (crisisData.fecha_programacion) {
        checkboxProgramas.checked = true;
        document.querySelector('#fecha_programacion_edit').value = crisisData.fecha_programacion;
    } else {
        checkboxProgramas.checked = false;
        document.querySelector('#fecha_programacion_edit').value = '';
    }

    // Manejar los canales digitales
    const canalesSeleccionados = crisisData.canal_cyc || [];
    document.querySelectorAll('[name="canal[]"]').forEach((checkbox) => {
        if (canalesSeleccionados.includes(checkbox.value)) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    });

    // Manejar los bots
    const botsSeleccionados = crisisData.bot_cyc || [];
    document.querySelectorAll('[name="bot[]"]').forEach((checkbox) => {
        if (botsSeleccionados.includes(checkbox.value)) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    });
}

// Ejemplo de solicitud para obtener datos de edición
function obtenerDatosCrisis(idCrisis) {
    const formData = new FormData();
    formData.append('action', 3);
    formData.append('id', idCrisis);

    fetch('../Controllers/crisis.php', { // Cambia esto por la ruta de tu archivo PHP
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.error) {
                console.error(data.error);
            } else {
                cargarDatosCrisis(data);
            }
        })
        .catch((error) => console.error('Error al obtener los datos:', error));
}



    // Función para filtrar la tabla por fecha
    $('#filterDate').on('change', function() {
        var selectedDate = $(this).val();
        table.column(6).search(selectedDate).draw();
    });

    // Función para restablecer los filtros
    $('#resetFiltersBtn').on('click', function() {
        $('#filterDate').val('');
        table.search('').column(6).search('').draw();  // Limpiar la búsqueda y restablecer los filtros
    });
});

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
            // Hacer una solicitud de eliminación
            window.location.href = `../Controllers/crisis.php?accion=4&id=${id}`;
        }
    });
}

// Función para activar/desactivar una crisis
function toggleCrisis(id, checkbox) {
    const status = checkbox.checked ? 1 : 0;  // Si está marcado, es 1 (prendido); si no, es 0 (apagado)
    const mensaje = status === 1 ? 
        '¿Está seguro que desea activar esta crisis o contingencia?' :
        '¿Está seguro que desea desactivar esta crisis o contingencia?';

    Swal.fire({
        title: '¿Estás seguro?',
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: status === 1 ? 'Sí, activar' : 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Hacer una solicitud para cambiar el estado de la crisis
            window.location.href = `../Controllers/crisis.php?accion=5&id=${id}&status=${status}`;
        } else {
            // Si se cancela, revertir el checkbox
            checkbox.checked = !checkbox.checked;
        }
    });
}
