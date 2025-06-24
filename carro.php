<?php
    // Iniciar sesión para manejar el carrito
    session_start();
    // Incluir archivo de conexión a la base de datos
    require_once 'conexion.php';

    // Procesar eliminación de producto del carrito
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
        // Obtener la referencia del producto a eliminar
        $referenciaEliminar = $_POST['referencia'];

        // Recorrer el carrito para encontrar el producto
        foreach ($_SESSION['carro'] as $index => $producto) {
            if ($producto['referencia'] === $referenciaEliminar) {
                // Si hay más de una unidad, reducir la cantidad
                if ($_SESSION['carro'][$index]['cantidad'] > 1) {
                    $_SESSION['carro'][$index]['cantidad'] -= 1;
                } else {
                    // Si es la última unidad, eliminar el producto del carrito
                    unset($_SESSION['carro'][$index]);
                    $_SESSION['carro'] = array_values($_SESSION['carro']); // Reindexar
                }
                break;
            }
        }

        // Redirigir al carrito
        header('Location: carro.php');
        exit;
    }

    // Procesar pago
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pagar'])) {
        // Verificar que el carrito no esté vacío
        if (!empty($_SESSION['carro'])) {
            // Iniciar transacción
            $conexion->begin_transaction();
            $success = true;

            try {
                // Procesar cada producto en el carrito
                foreach ($_SESSION['carro'] as $item) {
                    $referencia = $item['referencia'];
                    $cantidad = $item['cantidad'];

                    // Verificar el stock actual del producto
                    $stmt = $conexion->prepare("SELECT stock FROM recambios WHERE referencia = ?");
                    $stmt->bind_param("s", $referencia);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows === 0) {
                        $success = false;
                        throw new Exception("Producto con referencia $referencia no encontrado.");
                    }
                    $producto = $result->fetch_assoc();
                    if ($producto['stock'] < $cantidad) {
                        $success = false;
                        throw new Exception("Stock insuficiente para el producto con referencia $referencia.");
                    }
                    $stmt->close();

                    // Actualizar stock y cantidad vendida en la base de datos
                    $stmt = $conexion->prepare("UPDATE recambios SET stock = stock - ?, vendido = vendido + ? WHERE referencia = ?");
                    $stmt->bind_param("iis", $cantidad, $cantidad, $referencia);
                    if (!$stmt->execute()) {
                        $success = false;
                        throw new Exception("Error al actualizar el producto con referencia $referencia.");
                    }
                    $stmt->close();
                }

                // Confirmar la transacción si todo fue exitoso
                if ($success) {
                    $conexion->commit();
                    $_SESSION['carro'] = []; // Vaciar carrito
                    $mensaje_pago = "✅ ¡Gracias por tu compra!";
                    // Redirigir al inicio
                    header('Location: index.php');
                } else {
                    // Revertir la transacción en caso de error
                    $conexion->rollback();
                    $mensaje_pago = "❌ Error al procesar la compra. Por favor, intenta de nuevo.";
                }
            } catch (Exception $e) {
                // Revertir la transacción y registrar el error
                $conexion->rollback();
                $mensaje_pago = "❌ Error al procesar la compra: " . $e->getMessage();
                error_log($mensaje_pago); // Registrar error para depuración
            }
        } else {
            // Mostrar mensaje si el carrito está vacío
            $mensaje_pago = "❌ El carrito está vacío.";
        }
    }

    // Añadir producto al carrito
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['referencia'])) {
        // Normalizar los datos del producto entrante
        $producto = [
            'referencia' => $_POST['referencia'],
            'nombre'     => $_POST['nombre'],
            'precio'     => floatval($_POST['precio']),
            'cantidad'   => intval($_POST['cantidad']),
            'imagen'     => $_POST['imagen'] ?? 'img/default.jpg'
        ];

        if (!isset($_SESSION['carro'])) {
            $_SESSION['carro'] = [];
        }

        // Inicializar el carrito si no existe
        $encontrado = false;
        foreach ($_SESSION['carro'] as &$item) {
            // Coincidencia por referencia única del producto
            if ($item['referencia'] === $producto['referencia']) {
                // Sumar cantidades
                $item['cantidad'] += $producto['cantidad'];

                // Opcional: actualizar datos (por si cambian, para mantener consistencia)
                $item['nombre']  = $producto['nombre'];
                $item['precio']  = $producto['precio'];
                $item['imagen']  = $producto['imagen'];

                $encontrado = true;
                break;
            }
        }
        unset($item); // romper referencia

        // Si no estaba en el carrito, lo añadimos nuevo
        if (!$encontrado) {
            $_SESSION['carro'][] = $producto;
        }

        // Establecer mensaje de éxito
        $_SESSION['mensaje_carro'] = "✅ Producto añadido al carrito correctamente.";

        // Redirigir al inicio
        header('Location: index.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="css/carro.css">
</head>
<body>

<h2>🛒 Carrito de Compras</h2>

<!-- Mostrar mensaje de pago si existe -->
<?php if (isset($mensaje_pago)): ?>
    <div class="mensaje"><?php echo htmlspecialchars($mensaje_pago); ?></div>
<?php endif; ?>

<div class="carrito">
    <?php if (!empty($_SESSION['carro'])): ?>
        <?php 
        // Calcular el total a pagar
        $total_pagar = 0;
        foreach ($_SESSION['carro'] as $item): 
            $subtotal = $item['precio'] * $item['cantidad'];
            $total_pagar += $subtotal;
        ?>
            <!-- Mostrar cada producto en el carrito -->
            <div class="producto">
                <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="">
                <div class="producto-info">
                    <p><strong><?php echo htmlspecialchars($item['nombre']); ?></strong></p>
                    <p><?php echo number_format($item['precio'], 2); ?> € x <?php echo $item['cantidad']; ?> unidad(es)</p>
                    <p>Total: <strong><?php echo number_format($subtotal, 2); ?> €</strong></p>
                </div>
                <div class="acciones">
                    <!-- Formulario para eliminar producto -->
                    <form method="POST">
                        <input type="hidden" name="referencia" value="<?php echo htmlspecialchars($item['referencia']); ?>">
                        <button type="submit" name="eliminar" class="eliminar-btn">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="total">
            <p><strong>Total a pagar:</strong> <?php echo number_format($total_pagar, 2); ?> €</p>
        </div>

        <!-- Formulario para procesar el pago -->
        <form method="POST">
            <button type="submit" name="pagar" class="pagar-btn">💳 Pagar ahora</button>
        </form>
    <?php else: ?>
        <p style="text-align:center;">El carrito está vacío.</p>
    <?php endif; ?>
</div>

<a href="index.php" class="volver">← Seguir comprando</a>

</body>
</html>
<?php $conexion->close(); ?>