<?php
// guardar_producto.php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
require 'conexion.php';

function volverConError($mensaje, $id = null) {
    $destino = 'producto_form.php?error=' . urlencode($mensaje);
    if ($id) {
        $destino .= '&id=' . urlencode($id);
    }
    header('Location: ' . $destino);
    exit;
}

$id_producto  = $_POST['id_producto'] ?? '';
$codigo       = trim($_POST['codigo'] ?? '');
$nombre       = trim($_POST['nombre'] ?? '');
$descripcion  = trim($_POST['descripcion'] ?? '');
$precio       = $_POST['precio'] ?? '';
$stock_actual = $_POST['stock_actual'] ?? '';
$stock_minimo = $_POST['stock_minimo'] ?? '';
$id_categoria = $_POST['id_categoria'] ?? '';

// --- Validaciones en servidor (regla de negocio: nunca confiar solo en JS) ---
if ($codigo === '' || $nombre === '' || $id_categoria === '') {
    volverConError('Código, nombre y categoría son obligatorios.', $id_producto);
}
if (!is_numeric($precio) || $precio < 0) {
    volverConError('El precio no puede ser negativo.', $id_producto);
}
if (!ctype_digit((string)$stock_actual) || $stock_actual < 0) {
    volverConError('El stock actual no puede ser negativo.', $id_producto);
}
if (!ctype_digit((string)$stock_minimo) || $stock_minimo < 0) {
    volverConError('El stock mínimo no puede ser negativo.', $id_producto);
}

// --- Regla de negocio: el código no puede repetirse ---
if ($id_producto !== '') {
    // Editando: el código puede repetirse solo consigo mismo
    $stmt = $pdo->prepare('SELECT id_producto FROM producto WHERE codigo = ? AND id_producto != ?');
    $stmt->execute([$codigo, $id_producto]);
} else {
    $stmt = $pdo->prepare('SELECT id_producto FROM producto WHERE codigo = ?');
    $stmt->execute([$codigo]);
}
if ($stmt->fetch()) {
    volverConError('Ese código ya está en uso por otro producto.', $id_producto);
}

// --- Guardar (INSERT si es nuevo, UPDATE si trae id_producto) ---
if ($id_producto !== '') {
    $sql = 'UPDATE producto
            SET codigo = ?, nombre = ?, descripcion = ?, precio = ?,
                stock_actual = ?, stock_minimo = ?, id_categoria = ?
            WHERE id_producto = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$codigo, $nombre, $descripcion, $precio, $stock_actual, $stock_minimo, $id_categoria, $id_producto]);
} else {
    $sql = 'INSERT INTO producto (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria)
            VALUES (?, ?, ?, ?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$codigo, $nombre, $descripcion, $precio, $stock_actual, $stock_minimo, $id_categoria]);
}

header('Location: listado_productos.php');
exit;
