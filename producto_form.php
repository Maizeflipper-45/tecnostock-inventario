<?php
// producto_form.php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
require 'conexion.php';

// Traemos las categorías activas para el <select>
$categorias = $pdo->query("SELECT id_categoria, nombre FROM categoria WHERE estado = 'activo' ORDER BY nombre")->fetchAll();

// ¿Estamos editando? Si viene ?id=X, cargamos ese producto
$producto = [
    'id_producto' => '', 'codigo' => '', 'nombre' => '', 'descripcion' => '',
    'precio' => '', 'stock_actual' => '', 'stock_minimo' => '', 'id_categoria' => ''
];
$editando = false;

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM producto WHERE id_producto = ?');
    $stmt->execute([$_GET['id']]);
    $encontrado = $stmt->fetch();
    if ($encontrado) {
        $producto = $encontrado;
        $editando = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= $editando ? 'Editar' : 'Nuevo' ?> producto - TecnoStock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'includes/nav.php'; ?>

<div class="container mt-4" style="max-width: 600px;">
  <h4 class="mb-3"><?= $editando ? 'Editar producto' : 'Nuevo producto' ?></h4>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>

  <form id="formProducto" action="guardar_producto.php" method="POST" novalidate>
    <input type="hidden" name="id_producto" value="<?= htmlspecialchars($producto['id_producto']) ?>">

    <div class="mb-3">
      <label class="form-label">Código</label>
      <input type="text" class="form-control" name="codigo" required
             value="<?= htmlspecialchars($producto['codigo']) ?>">
      <div class="invalid-feedback">El código es obligatorio.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Nombre</label>
      <input type="text" class="form-control" name="nombre" required
             value="<?= htmlspecialchars($producto['nombre']) ?>">
      <div class="invalid-feedback">El nombre es obligatorio.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Descripción</label>
      <textarea class="form-control" name="descripcion" rows="2"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Categoría</label>
      <select class="form-select" name="id_categoria" required>
        <option value="">Selecciona una categoría</option>
        <?php foreach ($categorias as $c): ?>
          <option value="<?= $c['id_categoria'] ?>"
            <?= (string)$c['id_categoria'] === (string)$producto['id_categoria'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="invalid-feedback">Debes elegir una categoría.</div>
    </div>

    <div class="row">
      <div class="col mb-3">
        <label class="form-label">Precio</label>
        <input type="number" class="form-control" name="precio" min="0" step="1" required
               value="<?= htmlspecialchars($producto['precio']) ?>">
        <div class="invalid-feedback">El precio no puede ser negativo.</div>
      </div>
      <div class="col mb-3">
        <label class="form-label">Stock actual</label>
        <input type="number" class="form-control" name="stock_actual" min="0" step="1" required
               value="<?= htmlspecialchars($producto['stock_actual']) ?>">
        <div class="invalid-feedback">No puede ser negativo.</div>
      </div>
      <div class="col mb-3">
        <label class="form-label">Stock mínimo</label>
        <input type="number" class="form-control" name="stock_minimo" min="0" step="1" required
               value="<?= htmlspecialchars($producto['stock_minimo']) ?>">
        <div class="invalid-feedback">No puede ser negativo.</div>
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="listado_productos.php" class="btn btn-outline-secondary">Cancelar</a>
  </form>
</div>

<script>
// Validación en el cliente: rápida para el usuario, pero NUNCA sustituye la de PHP.
const form = document.getElementById('formProducto');
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
