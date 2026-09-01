<?php
// movimiento_form.php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
require 'conexion.php';

if (!isset($_GET['id_producto'])) {
    header('Location: listado_productos.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id_producto, codigo, nombre, stock_actual FROM producto WHERE id_producto = ?');
$stmt->execute([$_GET['id_producto']]);
$producto = $stmt->fetch();

if (!$producto) {
    header('Location: listado_productos.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar movimiento - TecnoStock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'includes/nav.php'; ?>

<div class="container mt-4" style="max-width: 500px;">
  <h4 class="mb-1">Registrar movimiento</h4>
  <p class="text-muted">
    <?= htmlspecialchars($producto['codigo']) ?> — <?= htmlspecialchars($producto['nombre']) ?><br>
    Stock actual: <strong><?= $producto['stock_actual'] ?></strong>
  </p>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>

  <form id="formMovimiento" action="guardar_movimiento.php" method="POST" novalidate>
    <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">

    <div class="mb-3">
      <label class="form-label">Tipo de movimiento</label>
      <select class="form-select" name="tipo" required>
        <option value="">Selecciona</option>
        <option value="entrada">Entrada</option>
        <option value="salida">Salida</option>
      </select>
      <div class="invalid-feedback">Debes elegir el tipo de movimiento.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Cantidad</label>
      <input type="number" class="form-control" name="cantidad" min="1" step="1" required>
      <div class="invalid-feedback">La cantidad debe ser mayor que cero.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Observación (opcional)</label>
      <textarea class="form-control" name="observacion" rows="2" placeholder="Ej: reposición de proveedor, venta local, producto dañado..."></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Registrar</button>
    <a href="listado_productos.php" class="btn btn-outline-secondary">Cancelar</a>
  </form>
</div>

<script>
const form = document.getElementById('formMovimiento');
form.addEventListener('submit', function (e) {
  if (!form.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
  }
  form.classList.add('was-validated');
});
</script>

</body>
</html>
