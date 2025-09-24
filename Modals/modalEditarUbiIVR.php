<!-- Modal de Edición de Ubicación IVR -->
<div class="modal fade" id="modalEditarUbicacionIVR" tabindex="-1" role="dialog" aria-labelledby="modalEditarUbicacionIVRLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarUbicacionIVRLabel">Editar Ubicación IVR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario de edición -->
                <form action="../Controllers/catUbicaciones.php" method="POST" id="form-editar-ubicacion-ivr">
                    <input type="text" name="accion" id="accion_editar" hidden value="3">
                    <input name="edit_id_ubicacion_ivr" hidden id="edit_id_ubicacion_ivr">
                    
                    <div class="mb-3">
                        <input type="text" name="nombre" id="edit_nombre_ubicacion_ivr" required class="form-control form-input" placeholder="Nombre de la ubicación IVR" aria-label="Nombre de la ubicación IVR">
                    </div>
                    
                    <div class="mb-3">
  <select name="edit_proyecto" id="edit_proyecto" class="form-select" required>
    <?php
    // Consulta todos los proyectos activos
    $queryProyectos = "SELECT id_proyecto, nombre_proyecto FROM cat_proyectos WHERE status = 1 ORDER BY nombre_proyecto ASC";
    $resultProyectos = $conn->query($queryProyectos);

    while ($row = $resultProyectos->fetch_assoc()) {
        echo "<option value='{$row['id_proyecto']}'>{$row['nombre_proyecto']}</option>";
    }
    ?>
  </select>
</div>
                    
                    <!-- Modal Footer -->
                    <div class="modal-footer d-flex justify-content-center">
                        <div class="btn-container">
                            <!-- Botón Guardar -->
                            <button type="submit" class="btn-icon" style="border-radius: 15px;">
                                <span>Guardar</span>
                                <img src="../iconos/Group-4.svg" alt="Guardar">
                            </button>
                            <!-- Botón Cancelar -->
                            <button type="button" class="btn-icon" style="border-radius: 15px;" data-bs-dismiss="modal">
                                <span>Cancelar</span>
                                <img src="../iconos/cancelar.png" alt="Cancelar">
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Cargar proyectos cuando se abra el modal de edición
document.getElementById('modalEditarUbicacionIVR').addEventListener('show.bs.modal', function () {
    cargarProyectos('edit_proyecto');
});

// Función para cargar proyectos en el select de edición
function cargarProyectos(selectId) {
    fetch('../Controllers/catUbicaciones.php?accion=6')
        .then(response => response.json())
        .then(proyectos => {
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Seleccionar proyecto</option>';
            
            proyectos.forEach(proyecto => {
                const option = document.createElement('option');
                option.value = proyecto.nombre_proyecto;
                option.textContent = proyecto.nombre_proyecto;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar proyectos:', error);
            Swal.fire('Error', 'No se pudieron cargar los proyectos', 'error');
        });
}
</script>

<!-- Estilos CSS adicionales -->
<style>
.modal-footer {
    display: flex;
    justify-content: center;
    align-items: center;
}

.btn-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
}

.btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    gap: 10px;
    background-color: #4B4A4B;
    border: none;
    color: white;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-icon:hover {
    background-color: #5A595A;
}

.btn-icon img {
    height: 24px;
    width: 24px;
}

.modal-body {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.form-control, .form-select {
    width: 80%;
    margin: 10px 0;
}

.form-input, .form-select {
    border: none;
    border-bottom: 2px solid #ccc;
    border-radius: 0;
    outline: none;
    background-color: transparent;
    padding: 10px;
}

.form-input:focus, .form-select:focus {
    border-bottom: 2px solid #007bff;
}
</style>