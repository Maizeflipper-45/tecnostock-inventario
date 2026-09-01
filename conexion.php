<?php
// conexion.php
// Archivo único de conexión a la base de datos, usado por todo el sistema.

$host   = '127.0.0.1';
$puerto = '3306';
$bd     = 'tecnostock';
$usuario_db = 'root';
$clave_db   = ''; 

$dsn = "mysql:host=$host;port=$puerto;dbname=$bd;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $usuario_db, $clave_db, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}
