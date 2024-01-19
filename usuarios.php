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
                $query_insert = mysqli_query($conexion, "INSERT INTO usuario(nombre, correo, usuario, clave, rol) VALUES ('$nombre', '$email', '$user', '$clave', '$rol')");

                if ($query_insert) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                Usuario Registrado
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>';
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
            $sql_update = mysqli_query($conexion, "UPDATE usuario SET nombre = '$nombre', correo = '$email', usuario = '$user', clave = '$clave', rol = '$rol' WHERE idusuario = $id");

            if ($sql_update) {
                $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Usuario Modificado
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" placeholder="Ingrese Nombre" name="nombre" id="nombre">
                        <input type="hidden" id="id" name="id">
                    </div>

                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="email" class="form-control" placeholder="Ingrese Correo Electrónico" name="correo" id="correo">
                    </div>

                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="usuario">Usuario</label>
                        <input type="text" class="form-control" placeholder="Ingrese Usuario" name="usuario" id="usuario">
                    </div>

                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="clave">Contraseña</label>
                        <input type="password" class="form-control" placeholder="Ingrese Contraseña" name="clave" id="clave">
                    </div>
                </div>
                <div class="col-md-3">
    <div class="form-group">
        <label for="rol">Rol</label>
        <select class="form-control" name="rol" id="rol">
            <option value="Vendedor" <?php echo ($rol == 'Vendedor') ? 'selected' : ''; ?>>Vendedor</option>
            <option value="Repartidor" <?php echo ($rol == 'Repartidor') ? 'selected' : ''; ?>>Repartidor</option>
        </select>
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
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($conexion, "SELECT * FROM usuario");
            $result = mysqli_num_rows($query);
            if ($result > 0) {
                while ($data = mysqli_fetch_assoc($query)) { ?>
                    <tr>

                        <td><?php echo $data['idusuario']; ?></td>
                        <td><?php echo $data['nombre']; ?></td>
                        <td><?php echo $data['correo']; ?></td>
                        <td><?php echo $data['usuario']; ?></td>
                        <td>
                            <a href="rol.php?id=<?php echo $data['idusuario']; ?>" class="btn btn-warning"><i class='fas fa-key'></i></a>
                            <a href="#" onclick="editarUsuario(<?php echo $data['idusuario']; ?>)" class="btn btn-success"><i class='fas fa-edit'></i></a>
                            <form action="eliminar_usuario.php?id=<?php echo $data['idusuario']; ?>" method="post" class="confirmar d-inline">
                                <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                            </form>
                        </td>
                    </tr>
            <?php }
            } ?>
        </tbody>
    </table>
</div>
<?php include_once "includes/footer.php"; ?>