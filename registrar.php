<?php
    // Iniciar la sesión para manejar variables de sesión
    session_start();
    // Incluir el archivo de conexión a la base de datos
    require_once 'conexion.php';
            
    //Recuperamos los datos del formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = trim($_POST['nombre']);
        $primer_apellido= trim($_POST['primer_apellido']);
        $segundo_apellido = trim($_POST['segundo_apellido']);
        $dni = trim($_POST['dni']);
        $telefono = trim($_POST['telefono']);
        $email= trim($_POST['email']);
        $direccion = trim($_POST['direccion']);
        $CPostal = trim($_POST['CPostal']);
        $n_tarjeta = trim($_POST['n_tarjeta']);
        $n_seguridad = trim($_POST['n_seguridad']);
        $fecha_cadu = trim($_POST['fecha_cadu']);
        $contrasena = trim($_POST['contrasena']);
                    
        // Verificar si el usuario ya está registrado
        $query = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $email); // $usuario es el valor del campo de usuario del formulario
        $stmt->execute();
        $result = $stmt->get_result();

        // Comprobar si el usuario ya existe
        if ($result->num_rows > 0) {
            echo "<script>alert('El usuario ya existe');
            window.location.href = 'registrar.html';
            </script>";
            exit; // Detiene la ejecución del script    
        } else {
           // Consulta preparada para evitar inyección SQL
           $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT); // Hash de la contraseña

           // Consulta preparada para insertar el nuevo usuario
           $sql = "INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, dni, telefono, email, direccion, CPostal, n_tarjeta, n_seguridad, fecha_cadu, contrasena) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
           $stmt = $conexion->prepare($sql);
           $stmt->bind_param("ssssssssssss", $nombre, $primer_apellido, $segundo_apellido, $dni, $telefono, $email, $direccion, $CPostal, $n_tarjeta, $n_seguridad, $fecha_cadu, $hashed_password);

           if($stmt->execute()) {
               session_start(); // Asegúrate de que está antes de cualquier output (ya lo tienes arriba)
               $_SESSION['nombre'] = $nombre;
               // Si la inserción fue exitosa, redirige al usuario
               header('Location: index.php');
               exit();
           } else {
               // Si hay un error en la inserción, mostrar un mensaje
               header('Location: registrar.html');
           }
        } 
        $stmt->close();   
    }    
?>   