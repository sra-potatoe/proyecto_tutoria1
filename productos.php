<?php
session_start();
include "../conexion.php";

$id_user = $_SESSION['idUser'];
$permiso = "productos";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);

if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}

if (!empty($_POST)) {
    $alert = "";
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];

    // Manejar la carga de imagen
    $imagen = $_FILES['imagen'];
    $imagen_nombre = $imagen['name'];
    $imagen_temp = $imagen['tmp_name'];

    // Ruta de destino (almacenará la imagen en la carpeta actual del script)
    $ruta_imagen = basename($imagen_nombre);

    if (empty($codigo) || empty($producto) || empty($precio) || $precio < 0 || empty($cantidad) || $cantidad < 0) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todos los campos son obligatorios
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        if (empty($id)) {
            $query = mysqli_query($conexion, "SELECT * FROM producto WHERE codigo = '$codigo'");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El código ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                if (move_uploaded_file($imagen_temp, $ruta_imagen)) {
                    $query_insert = mysqli_query($conexion, "INSERT INTO producto(codigo, descripcion, precio, existencia, imagen) VALUES ('$codigo', '$producto', '$precio', '$cantidad', '$ruta_imagen')");
                    if ($query_insert) {
                        $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Producto registrado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">
                        Error al registrar el producto
                        </div>';
                    }
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">
                        Error al mover la imagen
                        </div>';
                }
            }
        } else {
            if (move_uploaded_file($imagen_temp, $ruta_imagen)) {
                $query_update = mysqli_query($conexion, "UPDATE producto SET codigo = '$codigo', descripcion = '$producto', precio = $precio, existencia = $cantidad, imagen = '$ruta_imagen' WHERE codproducto = $id");
                if ($query_update) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Producto Modificado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Error al modificar
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                }
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                    Error al mover la imagen
                    </div>';
            }
        }
    }
}

include_once "includes/header.php";
?>
<div class="card shadow-lg">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form action="" method="post" enctype="multipart/form-data" autocomplete="off" id="formulario">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="codigo" class=" text-dark font-weight-bold"><i class="fas fa-barcode"></i> Código de Barras</label>
                                <input type="text" placeholder="Ingrese código de barras" name="codigo" id="codigo" class="form-control">
                                <input type="hidden" id="id" name="id">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="producto" class=" text-dark font-weight-bold">Producto</label>
                                <input type="text" placeholder="Ingrese nombre del producto" name="producto" id="producto" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="precio" class=" text-dark font-weight-bold">Precio</label>
                                <input type="text" placeholder="Ingrese precio" class="form-control" name="precio" id="precio">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="cantidad" class=" text-dark font-weight-bold">Cantidad</label>
                                <input type="number" placeholder="Ingrese cantidad" class="form-control" name="cantidad" id="cantidad">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Input para seleccionar la imagen -->
                            <label for="imagen" class="text-dark font-weight-bold">Imagen</label>
                            <input type="file" class="form-control-file" name="imagen" id="imagen">
                        </div>
                        <div class="col-md-6">
                            <input type="submit" value="Registrar" class="btn btn-primary" id="btnAccion">
                            <input type="button" value="Nuevo" onclick="limpiar()" class="btn btn-success" id="btnNuevo">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="tbl">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Imagen</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conexion, "SELECT * FROM producto");
                        $result = mysqli_num_rows($query);
                        if ($result > 0) {
                            while ($data = mysqli_fetch_assoc($query)) { ?>
                                <tr>
                                    <td><?php echo $data['codproducto']; ?></td>
                                    <td><?php echo $data['codigo']; ?></td>
                                    <td><?php echo $data['descripcion']; ?></td>
                                    <td><?php echo $data['precio']; ?></td>
                                    <td><?php echo $data['existencia']; ?></td>
                                    <td>
                                        <!-- Mostrar la imagen -->
                                        <img src="<?php echo $data['imagen']; ?>" alt="Imagen de producto" class="img-thumbnail" style="max-width: 100px;">
                                    </td>
                                    <td>
                                        <a href="#" onclick="editarProducto(<?php echo $data['codproducto']; ?>)" class="btn btn-primary"><i class='fas fa-edit'></i></a>

                                        <form action="eliminar_producto.php?id=<?php echo $data['codproducto']; ?>" method="post" class="confirmar d-inline">
                                            <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                                        </form>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>
