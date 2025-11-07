<?php
include("../Controllers/bd.php");

// Obtener perfiles
$queryPerfil = "SELECT * FROM perfil ORDER BY nombre_perfil ASC";
$perfil_data = $conn->prepare($queryPerfil);
$perfiles = [];

if ($perfil_data->execute()) {
    $result = $perfil_data->get_result();
    while ($row = $result->fetch_assoc()) {
        $perfiles[] = $row;
    }
}

// Obtener proyectos activos
$queryProyectos = "SELECT * FROM cat_proyectos WHERE status = 1 ORDER BY nombre_proyecto ASC";
$proyecto_data = $conn->prepare($queryProyectos);
$proyectos = [];

if ($proyecto_data->execute()) {
    $result = $proyecto_data->get_result();
    while ($row = $result->fetch_assoc()) {
        $proyectos[] = $row;
    }
}
?>

<!-- Modal -->
<div class="modal fade" id="NuevoUsuario" tabindex="-1" role="dialog" aria-labelledby="NuevoUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="NuevoUsuarioLabel">Nuevo Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <form action="../Controllers/usuarios.php" method="POST" id="form-usuario">
          <input type="hidden" name="accion" id="accion" value="1">

          <div class="mb-3">
            <input type="text" name="nombre_usuario" id="nombre_usuario" required class="form-control form-input" placeholder="Nombre de usuario">
          </div>

          <div class="mb-3">
            <input type="email" name="correo_usuario" id="correo_usuario" required class="form-control form-input" placeholder="Correo electrónico">
          </div>

          <div class="mb-3">
            <input type="text" name="puesto_usuario" id="puesto_usuario" required class="form-control form-input" placeholder="Puesto">
          </div>

          <div class="mb-3">
            <input type="text" name="telefono_usuario" id="telefono_usuario" required class="form-control form-input" placeholder="Teléfono">
          </div>

          <div class="mb-3">
            <select class="form-select form-input" required name="perfil_usuario" id="perfil_usuario">
              <option selected disabled class="d-none">Perfil de usuario</option>
              <?php foreach ($perfiles as $row): ?>
                  <option value="<?= $row['id_Perfil']; ?>"><?= htmlspecialchars($row['nombre_perfil']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Selección de proyecto, oculto por defecto -->
          <div class="mb-3" id="proyecto-container" >
            <select class="form-select form-input" name="proyecto" id="proyecto">
              <option selected disabled class="d-none">Selecciona un proyecto</option>
              <?php foreach ($proyectos as $p): ?>
                  <option value="<?= $p['id_proyecto']; ?>"><?= htmlspecialchars($p['nombre_proyecto']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <select class="form-select form-input" required name="status" id="status">
              <option value="1" selected>Activo</option>
              <option value="2">Inactivo</option>
            </select>
          </div>

          <input type="hidden" name="fecha_creacion" id="fecha_creacion" value="<?= date('Y-m-d H:i:s'); ?>" />

          <div class="modal-footer d-flex justify-content-center">
            <div class="btn-container">
              <button type="submit" class="btn-icon" style="border-radius: 15px;">
                <span>Guardar</span>
                <img src="../iconos/Group-4.svg" alt="Guardar">
              </button>

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

<!-- Script para mostrar/ocultar proyecto -->
<script>
document.getElementById('perfil_usuario').addEventListener('change', function() {
    var perfil = parseInt(this.value);
    var proyectoContainer = document.getElementById('proyecto-container');

    if (perfil === 1) { // Administrador
        proyectoContainer.style.display = 'none';
        document.getElementById('proyecto').required = false;
    } else {
        proyectoContainer.style.display = 'block';
        document.getElementById('proyecto').required = true;
    }
});
</script>

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
  .form-input {
    border: none;
    border-bottom: 2px solid #ccc;
    border-radius: 0;
    outline: none;
    background-color: transparent;
    padding: 10px;
  }
  .form-input:focus {
    border-bottom: 2px solid #007bff;
  }
</style>
