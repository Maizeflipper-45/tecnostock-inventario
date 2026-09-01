<?php
// guardar_movimiento.php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
require 'conexion.php';

function volverConError($mensaje, $id_producto) {
    header('Location: movimiento_form.php?id_producto=' . urlencode($id_producto) . '&error=' . urlencode($mensaje));
    exit;
}

$id_producto = $_POST['id_producto'] ?? '';
$tipo        = $_POST['tipo'] ?? '';
$cantidad    = $_POST['cantidad'] ?? '';
$observacion = trim($_POST['observacion'] ?? '');
$id_usuario  = $_SESSION['id_usuario'];

// --- Validaciones en servidor ---
if ($id_producto === '' || !in_array($tipo, ['entrada', 'salida'], true)) {
    volverConError('Datos inválidos.', $id_producto);
}
if (!ctype_digit((string)$cantidad) || (int)$cantidad <= 0) {
    volverConError('La cantidad debe ser un número mayor que cero.', $id_producto);
}
$cantidad = (int)$cantidad;

// Buscamos el producto y su stock actual
$stmt = $pdo->prepare('SELECT stock_actual FROM producto WHERE id_producto = ?');
$stmt->execute([$id_producto]);
$producto = $stmt->fetch();

if (!$producto) {
    header('Location: listado_productos.php');
    exit;
}

// Regla de negocio: no se puede sacar más stock del que hay
if ($tipo === 'salida' && $cantidad > $producto['stock_actual']) {
    volverConError('No hay stock suficiente para esa salida (stock actual: ' . $producto['stock_actual'] . ').', $id_producto);
}

// --- Transacción: el movimiento y la actualización de stock ocurren juntos, o no ocurre ninguno ---
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'INSERT INTO movimiento (tipo, cantidad, observacion, id_producto, id_usuario)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$tipo, $cantidad, $observacion, $id_producto, $id_usuario]);

    if ($tipo === 'entrada') {
        $sql = 'UPDATE producto SET stock_actual = stock_actual + ? WHERE id_producto = ?';
    } else {
        $sql = 'UPDATE producto SET stock_actual = stock_actual - ? WHERE id_producto = ?';
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cantidad, $id_producto]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    volverConError('Ocurrió un error al registrar el movimiento. Intenta nuevamente.', $id_producto);
}

header('Location: listado_productos.php');
exit;
