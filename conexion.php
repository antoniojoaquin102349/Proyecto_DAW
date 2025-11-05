<?php
    //Creamos una conexion desde php a mysql para mostrar datos y tb para ingresarlos
    $servidor = "localhost";
    $usuario = "root";
    $clave = "";
    $base = "proyecto";

    //Conectar una base de datos usamos mysqli_connect
    $conexion = mysqli_connect($servidor, $usuario, $clave, $base);

    //Probar que la conexion se haya realizado
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
?>