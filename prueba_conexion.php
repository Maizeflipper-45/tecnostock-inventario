<?php
require 'conexion.php';

echo '<h2>Prueba de conexión a TecnoStock</h2>';

$stmt = $pdo->query('SELECT id_producto, codigo, nombre, precio, stock_actual FROM producto');
$productos = $stmt->fetchAll();

if (count($productos) === 0) {
    echo '<p>Conexión OK, pero no hay productos cargados.</p>';
} else {
    echo '<p>Conexión exitosa. Productos encontrados:</p>';
    echo '<table border="1" cellpadding="6">';
    echo '<tr><th>ID</th><th>Código</th><th>Nombre</th><th>Precio</th><th>Stock</th></tr>';
    foreach ($productos as $p) {
        echo '<tr>';
        echo '<td>' . $p['id_producto'] . '</td>';
        echo '<td>' . htmlspecialchars($p['codigo']) . '</td>';
        echo '<td>' . htmlspecialchars($p['nombre']) . '</td>';
        echo '<td>$' . number_format($p['precio'], 0, ',', '.') . '</td>';
        echo '<td>' . $p['stock_actual'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
