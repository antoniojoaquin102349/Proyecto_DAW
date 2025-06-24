<?php
    // Iniciar la sesión para manejar el carrito
    session_start();
    // Incluir el archivo de conexión a la base de datos
    include_once 'conexion.php';

    // Inicializar el carrito si no existe
    if (!isset($_SESSION['carro'])) {
        $_SESSION['carro'] = [];
    }

    $mensaje = null;

    // Añadir al carrito si se envió por POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comprar'])) {
        // Obtener la referencia del producto
        $referencia = $_POST['referencia'];
        // Obtener la cantidad, con valor por defecto de 1
        $cantidad = intval($_POST['cantidad'] ?? 1);

        $encontrado = false;

         // Verificar si el producto ya está en el carrito
        foreach ($_SESSION['carro'] as &$item) {
            if ($item['referencia'] === $referencia) {
                // Incrementar la cantidad del producto existente
                $item['cantidad'] += $cantidad;

                // Puedes actualizar otros campos si lo deseas
                $item['nombre'] = $_POST['nombre'];
                $item['precio'] = $_POST['precio'];
                $item['descripcion'] = $_POST['descripcion'];
                $item['imagen'] = $_POST['imagen'];

                $encontrado = true;
                break;
            }
        }
        unset($item); // Romper referencia

        // Si el producto no está en el carrito, añadirlo
        if (!$encontrado) {
            $_SESSION['carro'][] = [
                'referencia' => $referencia,
                'nombre' => $_POST['nombre'],
                'precio' => $_POST['precio'],
                'descripcion' => $_POST['descripcion'],
                'imagen' => $_POST['imagen'],
                'cantidad' => $cantidad
            ];
        }

        // Establecer mensaje de éxito
        $mensaje = "✅ Producto añadido al carrito.";
        // Redirigir a la misma página para evitar reenvío del formulario
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }


    // Determinar si se filtra por categoría o búsqueda
    $categoria = isset($_GET['categoria']) ? $conexion->real_escape_string($_GET['categoria']) : null;
    $busqueda = isset($_GET['busqueda']) ? $conexion->real_escape_string(trim($_GET['busqueda'])) : null;

    // Consultar los productos
    $sql = "SELECT referencia, nombre, precio, descripcion, imagen FROM recambios";
    $conditions = [];

    // Añadir condiciones de filtrado si existen
    if ($categoria) {
        $conditions[] = "categoria = '$categoria'";
    }
    if ($busqueda) {
        $conditions[] = "(referencia LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%')";
    }

    // Agregar condiciones a la consulta si existen
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $resultado = $conexion->query($sql);
    $productos = [];

    // Almacenar los productos en un array
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos <?php echo $categoria ? '- ' . htmlspecialchars(ucfirst($categoria)) : ($busqueda ? '- Resultados de búsqueda' : ''); ?></title>
    <link rel="stylesheet" href="css/productos.css">
</head>
<body>
    <!-- Formulario de búsqueda -->
    <form method="GET" action="productos.php" class="search-form">
        <input type="text" name="busqueda" placeholder="Buscar por referencia o nombre..." 
               value="<?php echo $busqueda ? htmlspecialchars($busqueda) : ''; ?>" required>
        <button type="submit" class="search-btn">Buscar</button>
    </form>

    <h1 class="section-title">
        <?php
            if ($busqueda) {
                echo 'Resultados de búsqueda para: ' . htmlspecialchars($busqueda);
            } elseif ($categoria) {
                echo 'Productos en: ' . htmlspecialchars(ucfirst($categoria));
            } else {
                echo 'Listado de Recambios';
            }
        ?>
    </h1>
    <!-- Enlace al carrito con contador de productos -->
    <a href="carro.php" class="btn-carro">🛒 Ver carrito (<?php echo count($_SESSION['carro']); ?>)</a>

    <?php if ($mensaje): ?>
        <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <div class="grid">
    <?php if (!empty($productos)): ?>
        <?php foreach ($productos as $producto): ?>
            <div class="item">
                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                <p><strong>Referencia:</strong> <?php echo htmlspecialchars($producto['referencia']); ?></p>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($producto['nombre']); ?></p>
                <p><strong>Precio:</strong> <?php echo number_format($producto['precio'], 2); ?> €</p>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <form method="POST">
                    <input type="hidden" name="referencia" value="<?php echo htmlspecialchars($producto['referencia']); ?>">
                    <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <input type="hidden" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>">
                    <input type="hidden" name="descripcion" value="<?php echo htmlspecialchars($producto['descripcion']); ?>">
                    <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($producto['imagen']); ?>">
                    <input type="number" name="cantidad" value="1" min="1" required>
                    <button type="submit" name="comprar" class="comprar-btn">Añadir al carrito</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center;">No hay productos disponibles<?php echo $busqueda ? ' para esta búsqueda' : ($categoria ? ' en esta categoría' : ''); ?>.</p>
    <?php endif; ?>
</div>

    <div style="text-align: center;">
        <a href="index.php" class="btn-volver">← Volver a la página principal</a>
    </div>

    <!-- Script para ocultar el mensaje después de 3 segundos -->
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
    $conexion->close();
?>