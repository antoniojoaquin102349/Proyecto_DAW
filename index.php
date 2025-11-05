<?php
// Iniciar la sesión para manejar variables de sesión (por ejemplo, usuario logueado, mensajes del carrito)
session_start();

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

// Inicializar el arreglo para almacenar los productos destacados
$productos_destacados = [];

// Consulta SQL para obtener hasta 4 productos con stock disponible, ordenados por los más vendidos
$sql = "SELECT referencia, nombre, precio, descripcion, imagen, stock 
        FROM recambios 
        WHERE stock > 0 
        ORDER BY vendido DESC 
        LIMIT 4";

// Ejecutar la consulta
$resultado = $conexion->query($sql);

// Verificar si la consulta fue exitosa
if ($resultado) {
    // Si hay resultados, almacenarlos en el arreglo $productos_destacados
    if ($resultado->num_rows > 0) {
        $productos_destacados = $resultado->fetch_all(MYSQLI_ASSOC);
    }
} else {
    // Registrar el error en el log del servidor para depuración
    error_log("Error en la consulta de productos destacados: " . $conexion->error);
    // Mostrar un mensaje amigable al usuario
    echo '<div class="mensaje error">Error al cargar los productos destacados. Por favor, intenta de nuevo más tarde.</div>';
}

// Liberar el resultado de la consulta
if ($resultado) {
    $resultado->free();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Definir la codificación de caracteres -->
    <meta charset="UTF-8">
    <!-- Hacer la página responsiva -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título de la página -->
    <title>Repuestos Carmona</title>
    <!-- Enlazar el archivo de estilos CSS -->
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <!-- Encabezado principal de la página -->
    <header>
        <!-- Mostrar un saludo personalizado según el usuario logueado -->
        <div>Hola, <?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Invitado'; ?></div>
        <!-- Formulario de búsqueda -->
        <form method="GET" action="productos.php" class="search-form">
            <input type="text" name="busqueda" placeholder="Buscar por referencia o nombre..." required>
            <button type="submit" class="search-btn">Buscar</button>
        </form>
        <button class="btn-cv" onclick="window.open('Currículum.pdf', '_blank')">Ver Currículum</button>
        <!-- Menú de navegación principal -->
        <nav>
            <!-- Menú desplegable para categorías -->
            <div class="dropdown">
                <a href="#">Categorías ▾</a>
                <div class="dropdown-content">
                    <a href="productos.php?categoria=Carroceria">Carrocería</a>
                    <a href="productos.php?categoria=Suspension">Suspensión</a>
                    <a href="productos.php?categoria=Mecanica">Mecánica</a>
                    <a href="productos.php?categoria=Ruedas">Ruedas</a>
                    <a href="productos.php?categoria=Electricidad">Electricidad</a>
                    <a href="productos.php?categoria=Accesorios">Accesorios</a>
                </div>
            </div>
            <!-- Enlace al carrito de compras -->
            <a href="carro.php">Carrito</a>
            <!-- Enlace a la página "Acerca de" -->
            <a href="acerca_de.html">Acerca de</a>
            <!-- Enlace condicional según el estado de la sesión -->
            <?php if (isset($_SESSION['nombre'])): ?>
                <a href="cerrar_sesion.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="iniciar_sesion.html">Iniciar sesión</a>
            <?php endif; ?>
            <!-- Enlace a la página de registro -->
            <a href="registrar.html">Registrate</a>
        </nav>
    </header>

    <!-- Mostrar mensaje de confirmación del carrito si existe -->
    <?php if (isset($_SESSION['mensaje_carro'])): ?>
        <div class="mensaje"><?php echo htmlspecialchars($_SESSION['mensaje_carro']); ?></div>
        <?php unset($_SESSION['mensaje_carro']); ?>
    <?php endif; ?>

    <!-- Sección hero con imagen de fondo y llamada a la acción -->
    <section class="hero">
        <h1>ENCUENTRA LOS MEJORES REPUESTOS 4X4</h1>
        <!-- Botón para dirigir a la página de productos -->
        <a href="productos.php" class="comprar">Comprar ahora</a>
    </section>

    <!-- Sección de productos destacados -->
    <section class="products">
        <h2 class="section-title">Productos destacados</h2>
        <div class="grid">
            <!-- Verificar si hay productos destacados para mostrar -->
            <?php if (!empty($productos_destacados)): ?>
                <?php foreach ($productos_destacados as $producto): ?>
                    <!-- Elemento individual de producto -->
                    <div class="item">
                        <!-- Imagen del producto con protección contra XSS -->
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <p>Referencia: <?php echo htmlspecialchars($producto['referencia']); ?></p>
                        <p>Nombre: <?php echo htmlspecialchars($producto['nombre']); ?></p>
                        <!-- Formatear el precio con dos decimales -->
                        <p>Precio: <?php echo number_format($producto['precio'], 2); ?> €</p>
                        <p>Descripción: <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <!-- Formulario para añadir el producto al carrito -->
                        <form method="POST" action="carro.php">
                            <input type="hidden" name="referencia" value="<?php echo htmlspecialchars($producto['referencia']); ?>">
                            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <input type="hidden" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>">
                            <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($producto['imagen']); ?>">
                            <!-- Campo para seleccionar la cantidad, limitada por el stock disponible -->
                            <input type="number" name="cantidad" value="1" min="1" 
                                   max="<?php echo htmlspecialchars($producto['stock']); ?>" 
                                   required>
                            <button type="submit" class="btn-comprar">Añadir al carrito</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Mensaje si no hay productos disponibles -->
                <p style="text-align:center;">No hay productos destacados disponibles.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Script para ocultar el mensaje de confirmación después de 3 segundos -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mensaje = document.querySelector('.mensaje');
            if (mensaje) {
                setTimeout(() => {
                    mensaje.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>
</html>
<?php
// Cerrar la conexión a la base de datos
$conexion->close();
?>