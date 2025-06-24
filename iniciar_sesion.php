<?php
    // Iniciar la sesión para manejar variables de sesión
    session_start();
    // Incluir el archivo de conexión a la base de datos
    require_once 'conexion.php';

    //Si el usuario ya inició sesión, lo redirigimos a la página principal
    if (isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    }

    // Verificamos si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = trim($_POST['email']);
        $contrasena = trim($_POST['contrasena']);

        // Preparar la consulta para buscar el usuario por email
        $stmt = $conexion->prepare("SELECT email, contrasena, nombre FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // Verificar si se encontró el usuario
        if ($resultado && $resultado->num_rows > 0) {
            // Usuario encontrado, verificar la contraseña
            // Aquí asumimos que la contraseña está hasheada con password_hash()
            $fila = $resultado->fetch_assoc();
            // Verificar la contraseña ingresada con la almacenada en la base de datos
            // Si la contraseña está hasheada, usamos password_verify()
            if (password_verify($contrasena, $fila['contrasena'])) {
                $_SESSION['email'] = $email; // Iniciar sesión
                $_SESSION['nombre'] = $fila['nombre']; // <-- Aquí guardas el nombre
                header('Location: index.php'); // Redirige normalmente
                exit();
            } else {
                // Contraseña incorrecta
                echo "<script>alert('contraseña incorrecta'); window.location.href = 'iniciar_sesion.html';</script>";
                exit();
            }
        } else {
            // Mostrar alerta si el usuario no existe y redirigir al registro
            echo "<script>alert('Usuario no encontrado'); window.location.href = 'registrar.html';</script>";
            exit();
        }
    }
    
    

    require_once 'cerrar_conexion.php';
?>
