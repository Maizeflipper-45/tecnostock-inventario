<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar sesión - TecnoStock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
    <div class="card-body p-4">
      <h4 class="card-title text-center mb-4">TecnoStock</h4>
      <p class="text-center text-muted mb-4">Sistema de inventario</p>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger py-2">
          <?php
            if ($_GET['error'] === 'credenciales') {
              echo 'Correo o contraseña incorrectos.';
            } elseif ($_GET['error'] === 'vacio') {
              echo 'Debes completar ambos campos.';
            }
          ?>
        </div>
      <?php endif; ?>

      <form action="procesar_login.php" method="POST" novalidate>
        <div class="mb-3">
          <label for="correo" class="form-label">Correo</label>
          <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <div class="mb-3">
          <label for="clave" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="clave" name="clave" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
