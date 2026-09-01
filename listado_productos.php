<?php
// listado_productos.php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
require 'conexion.php';
 
// Término de búsqueda (opcional, viene de ?buscar=...)
$buscar = trim($_GET['buscar'] ?? '');
 
if ($buscar !== '') {
    // Buscamos por nombre, código o nombre de categoría al mismo tiempo
    $sql = 'SELECT p.id_producto, p.codigo, p.nombre, p.precio, p.stock_actual, p.stock_minimo,
                   p.estado, c.nombre AS categoria
            FROM producto p
            JOIN categoria c ON c.id_categoria = p.id_categoria
            WHERE p.nombre LIKE :busq1 OR p.codigo LIKE :busq2 OR c.nombre LIKE :busq3
            ORDER BY p.nombre';
    $stmt = $pdo->prepare($sql);
    $termino = '%' . $buscar . '%';
    $stmt->execute([
        'busq1' => $termino,
        'busq2' => $termino,
        'busq3' => $termino,
    ]);
} else {
    $sql = 'SELECT p.id_producto, p.codigo, p.nombre, p.precio, p.stock_actual, p.stock_minimo,
                   p.estado, c.nombre AS categoria
            FROM producto p
            JOIN categoria c ON c.id_categoria = p.id_categoria
            ORDER BY p.nombre';
    $stmt = $pdo->query($sql);
}
$productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Productos - TecnoStock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
 
<?php include 'includes/nav.php'; ?>
 
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Productos</h4>
    <a href="producto_form.php" class="btn btn-success">+ Nuevo producto</a>
  </div>
 
  <form method="GET" class="row g-2 mb-3">
    <div class="col-auto flex-grow-1">
      <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, código o categoría"
             value="<?= htmlspecialchars($buscar) ?>">
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary">Buscar</button>
      <?php if ($buscar !== ''): ?>
        <a href="listado_productos.php" class="btn btn-outline-secondary">Limpiar</a>
      <?php endif; ?>
    </div>
  </form>
 
  <?php if (count($productos) === 0): ?>
    <div class="alert alert-info">No se encontraron productos.</div>
  <?php else: ?>
    <table class="table table-bordered table-hover bg-white">
      <thead class="table-dark">
        <tr>
          <th>Código</th>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($productos as $p): ?>
          <?php $stockBajo = $p['stock_actual'] < $p['stock_minimo']; ?>
          <tr class="<?= $stockBajo ? 'table-warning' : '' ?>">
            <td><?= htmlspecialchars($p['codigo']) ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= htmlspecialchars($p['categoria']) ?></td>
            <td>$<?= number_format($p['precio'], 0, ',', '.') ?></td>
            <td>
              <?= $p['stock_actual'] ?>
              <?php if ($stockBajo): ?>
                <span class="badge bg-danger ms-1">Stock bajo</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($p['estado'] === 'activo'): ?>
                <span class="badge bg-success">Activo</span>
              <?php else: ?>
                <span class="badge bg-secondary">Inactivo</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="producto_form.php?id=<?= $p['id_producto'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
              <a href="movimiento_form.php?id_producto=<?= $p['id_producto'] ?>" class="btn btn-sm btn-outline-success">Movimiento</a>
              <a href="cambiar_estado_producto.php?id=<?= $p['id_producto'] ?>"
                 class="btn btn-sm <?= $p['estado'] === 'activo' ? 'btn-outline-danger' : 'btn-outline-secondary' ?>"
                 onclick="return confirm('<?= $p['estado'] === 'activo' ? '¿Desactivar' : '¿Reactivar' ?> este producto?');">
                <?= $p['estado'] === 'activo' ? 'Desactivar' : 'Reactivar' ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
 
</body>
</html>