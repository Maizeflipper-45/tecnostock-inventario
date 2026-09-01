<?php
// index.php
session_start();
 
// Si no hay sesión iniciada, no se puede ver esta página
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>TecnoStock - Inicio</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
 
<?php include 'includes/nav.php'; ?>
 
<div class="container mt-4">
  <h4>Bienvenido/a al sistema de inventario</h4>
  <p class="text-muted">Desde aquí vamos a construir el listado de productos, el registro de movimientos y las alertas de stock bajo.</p>
</div>
 
</body>
</html>
