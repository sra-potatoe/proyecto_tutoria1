<?php
session_start();
$permiso = 'usuarios';
$id_user = $_SESSION['idUser'];
include "../conexion.php";

// Verificar permisos del usuario
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");

if (!$sql) {
    // Manejar el error de consulta
    die('Error en la consulta SQL: ' . mysqli_error($conexion));
}

$existe = mysqli_fetch_all($sql, MYSQLI_ASSOC);

if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}

// Obtener roles de la base de datos
$rolesQuery = mysqli_query($conexion, "SELECT DISTINCT rol FROM usuario");

if (!$rolesQuery) {
    // Manejar el error de consulta
    die('Error en la consulta SQL: ' . mysqli_error($conexion));
}

$roles = mysqli_fetch_all($rolesQuery, MYSQLI_ASSOC);

// Procesar formulario
if (!empty($_POST)) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['correo'];
    $user = $_POST['usuario'];
    $clave = !empty($_POST['clave']) ? md5($_POST['clave']) : null;
    $rol = $_POST['rol'];
    $horario_trabajo = $_POST['horario_trabajo'];
    $modalidad_traslado = $_POST['modalidad_traslado'];

    $alert = "";

    if (empty($nombre) || empty($email) || empty($user) || empty($rol)) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Todos los campos son obligatorios, incluyendo el rol.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
    } else {
        if (empty($id)) {
            // Insertar nuevo usuario
            $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE correo = '$email'");
            $result = mysqli_fetch_array($query);

            if ($result > 0) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                            El correo ya existe
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
            } else {
                $query_insert_user = mysqli_query($conexion, "INSERT INTO usuario(nombre, correo, usuario, clave, rol) VALUES ('$nombre', '$email', '$user', '$clave', '$rol')");

                if ($query_insert_user) {
                    $id_usuario = mysqli_insert_id($conexion); // Obtener el ID del usuario recién insertado

                    // Insertar información de repartidor
                    $query_insert_repartidor = mysqli_query($conexion, "INSERT INTO repartidor(idusuario, horario_trabajo, modalidad_traslado) VALUES ('$id_usuario', '$horario_trabajo', '$modalidad_traslado')");

                    if ($query_insert_repartidor) {
                        $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Repartidor Registrado
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>';
                    } else {
                        $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Error al registrar como repartidor
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>';
                    }
                } else {
                    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Error al registrar
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>';
                }
            }
        } else {
            // Actualizar usuario existente
            $sql_update_user = mysqli_query($conexion, "UPDATE usuario SET nombre = '$nombre', correo = '$email', usuario = '$user', clave = '$clave', rol = '$rol' WHERE idusuario = $id");

            if ($sql_update_user) {
                // Actualizar información de repartidor
                $sql_update_repartidor = mysqli_query($conexion, "UPDATE repartidor SET horario_trabajo = '$horario_trabajo', modalidad_traslado = '$modalidad_traslado' WHERE idusuario = $id");

                if ($sql_update_repartidor) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                Repartidor Modificado
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>';
                } else {
                    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Error al modificar como repartidor
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>';
                }
            } else {
                $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Error al modificar
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
            }
        }
    }
}

include "includes/header.php";
?>

<div class="card">
    <div class="card-body">
        <form action="" method="post" autocomplete="off" id="formulario">
            <?php echo isset($alert) ? $alert : ''; ?>
            <div class="row">
                <!-- ... Otros campos ... -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="horario_trabajo">Horario de Trabajo</label>
                        <input type="text" class="form-control" placeholder="Ingrese Horario de Trabajo" name="horario_trabajo" id="horario_trabajo">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="modalidad_traslado">Modalidad de Traslado</label>
                        <input type="text" class="form-control" placeholder="Ingrese Modalidad de Traslado" name="modalidad_traslado" id="modalidad_traslado">
                    </div>
                </div>
            </div>
            <input type="submit" value="Registrar" class="btn btn-primary" id="btnAccion">
            <input type="button" value="Nuevo" class="btn btn-success" id="btnNuevo" onclick="limpiar()">
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-striped table-bordered mt-2" id="tbl">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Horario de Trabajo</th>
                <th>Modalidad de Traslado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($conexion, "SELECT u.idusuario, u.nombre, u.correo, u.usuario, r.horario_trabajo, r.modalidad_traslado FROM usuario u INNER JOIN repartidor r ON u.idusuario = r.idusuario");
            $result = mysqli_num_rows($query);

            if ($result > 0) {
                while ($data = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><?php echo $data['idusuario']; ?></td>
                        <td><?php echo $data['nombre']; ?></td>
                        <td><?php echo $data['correo']; ?></td>
                        <td><?php echo $data['usuario']; ?></td>
                        <td><?php echo $data['horario_trabajo']; ?></td>
                        <td><?php echo $data['modalidad_traslado']; ?></td>
                        <td>
                            <!-- ... Otros botones ... -->
                        </td>
                    </tr>
            <?php }
            } ?>
        </tbody>
    </table>
</div>

<?php include_once "includes/footer.php"; ?>
