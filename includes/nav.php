<?php
// includes/nav.php
// Se incluye en cada página protegida. Espera que $_SESSION['nombre'] ya exista.
?>
<nav class="navbar navbar-expand navbar-dark bg-dark">
  <div class="container">
    <span class="navbar-brand mb-0 h1">TecnoStock</span>
    <div class="navbar-nav me-auto">
      <a class="nav-link text-white" href="index.php">Inicio</a>
      <a class="nav-link text-white" href="listado_productos.php">Productos</a>
    </div>
    <div class="d-flex align-items-center">
      <span class="text-white me-3">Hola, <?= htmlspecialchars($_SESSION['nombre']) ?></span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
    </div>
  </div>
</nav>