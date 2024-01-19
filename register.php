<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pet-shop";

// Crea la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verifica si todas las variables necesarias están definidas
if(isset($_POST['nombre']) && isset($_POST['telefono']) && isset($_POST['email']) && isset($_POST['direccion']) && isset($_POST['password'])){
    // Asigna los valores de las variables
    $name = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $password = md5($_POST['password']); // Recuerda aplicar md5() a la contraseña para mayor seguridad

    // Crear la consulta SQL para la inserción
    $sql = "INSERT INTO cliente (nombre, telefono, email, direccion, password) VALUES ('$name', '$telefono', '$email', '$direccion', '$password')";

    // Ejecutar la consulta
    if ($conn->query($sql) === TRUE) {
        echo "success"; // Éxito en la inserción
    } else {
        echo "failure"; // Error en la inserción
    }
}

// Cierra la conexión después de ejecutar las consultas
$conn->close();
?>
