<?php
// procesar_login.php
session_start();
require 'conexion.php';

$correo = trim($_POST['correo'] ?? '');
$clave  = $_POST['clave'] ?? '';

// Validación básica en servidor (nunca confiar solo en JS)
if ($correo === '' || $clave === '') {
    header('Location: login.php?error=vacio');
    exit;
}

$stmt = $pdo->prepare('SELECT id_usuario, nombre, clave, estado FROM usuario WHERE correo = ?');
$stmt->execute([$correo]);
$usuario = $stmt->fetch();

// Verificamos: que exista el usuario, que la clave coincida con el hash, y que esté activo
if (!$usuario || !password_verify($clave, $usuario['clave']) || $usuario['estado'] !== 'activo') {
    header('Location: login.php?error=credenciales');
    exit;
}

// Login correcto: guardamos datos mínimos en la sesión
$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['nombre']     = $usuario['nombre'];

header('Location: index.php');
exit;
