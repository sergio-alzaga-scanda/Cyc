<?php
include("../Controllers/bd.php");

$queryPerfil = "SELECT * FROM perfil ORDER BY nombre_perfil ASC";
$perfil_data = $conn->prepare($queryPerfil);
$proyecto_id = $_SESSION['proyecto'];
$perfiles = [];

if ($perfil_data->execute()) {
    $result = $perfil_data->get_result(); // <- Obtener resultado
    while ($row = $result->fetch_assoc()) {
        $perfiles[] = $row;
    }
} else {
    echo "Error al obtener los perfiles";
}

// Procesar formulario para insertar usuario vulnerable a SQLi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] == 1) {
    // Datos recibidos (sin sanitizar para mostrar vulnerabilidad)
    $nombre = $_POST['nombre_usuario'];
    $correo = $_POST['correo_usuario'];
    $puesto = $_POST['puesto_usuario'];
    $telefono = $_POST['telefono_usuario'];
    $perfil = $_POST['perfil_usuario'];
    $status = $_POST['status'];
    $fecha = $_POST['fecha_creacion'];

    // Consulta vulnerable a SQL Injection (NO USAR EN PRODUCCIÓN)
    $sql = "INSERT INTO usuarios (nombre_usuario, correo_usuario, puesto_usuario, telefono_usuario, perfil_usuario, status, fecha_creacion) VALUES (
        '$nombre',
        '$correo',
        '$puesto',
        '$telefono',
        $perfil,
        $status,
        '$fecha'
    )";

    // Ejecutar la consulta
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Usuario guardado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<!-- Modal HTML (puedes adaptarlo fuera del modal si quieres) -->
<div class="modal fade show" id="NuevoUsuario" tabindex="-1" role="dialog" aria-labelledby="NuevoUsuarioLabel" aria-modal="true" style="display:block;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="NuevoUsuarioLabel">Nuevo Usuario</h5>
      </div>
      <div class="modal-body">
        <form action="" method="POST" id="form-usuario">
          <input type="hidden" name="accion" id="accion" value="1">

          <div class="mb-3">
            <input type="text" name="nombre_usuario" id="nombre_usuario" required class="form-control form-input" placeholder="Nombre de usuario" aria-label="Nombre de usuario">
          </div>
          <div class="mb-3">
            <input type="email" name="correo_usuario" id="correo_usuario" required class="form-control form-input" placeholder="Correo electrónico" aria-label="Correo electrónico">
          </div>
          <div class="mb-3">
            <input type="text" name="puesto_usuario" id="puesto_usuario" required class="form-control form-input" placeholder="Puesto" aria-label="Puesto">
          </div>
          <div class="mb-3">
            <input type="text" name="telefono_usuario" id="telefono_usuario" required class="form-control form-input" placeholder="Teléfono" aria-label="Teléfono">
          </div>
          <div class="mb-3">
            <select class="form-select form-input" required name="perfil_usuario" id="perfil_usuario">
              <option selected disabled class="d-none">Perfil de usuario</option>
              <?php
                foreach ($perfiles as $row) {
                    echo '<option value="' . $row['id_Perfil'] . '">' . htmlspecialchars($row['nombre_perfil']) . '</option>';
                }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <select class="form-select form-input" required name="status" id="status">
              <option value="1" selected>Activo</option>
              <option value="2">Inactivo</option>
            </select>
          </div>

          <input type="hidden" name="fecha_creacion" id="fecha_creacion" value="<?php echo date('Y-m-d H:i:s'); ?>" />

          <div class="modal-footer d-flex justify-content-center">
            <div class="btn-container">
              <button type="submit" class="btn-icon" style="border-radius: 15px;">
                <span>Guardar</span>
                <img src="../iconos/Group-4.svg" alt="Guardar">
              </button>

              <button type="button" class="btn-icon" style="border-radius: 15px;" onclick="window.history.back();">
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
