<?php
// cambiar_estado_producto.php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
require 'conexion.php';

$id_producto = $_GET['id'] ?? '';

if ($id_producto === '') {
    header('Location: listado_productos.php');
    exit;
}

$stmt = $pdo->prepare('SELECT estado FROM producto WHERE id_producto = ?');
$stmt->execute([$id_producto]);
$producto = $stmt->fetch();

if (!$producto) {
    header('Location: listado_productos.php');
    exit;
}

// Alternamos el estado: si estaba activo, pasa a inactivo, y viceversa.
// Nunca se hace DELETE — así se conserva el historial de movimientos asociado.
$nuevoEstado = $producto['estado'] === 'activo' ? 'inactivo' : 'activo';

$stmt = $pdo->prepare('UPDATE producto SET estado = ? WHERE id_producto = ?');
$stmt->execute([$nuevoEstado, $id_producto]);

header('Location: listado_productos.php');
exit;
