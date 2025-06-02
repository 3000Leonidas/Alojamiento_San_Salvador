<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"), true);

$nombre_usuario = $data['nombre_usuario'] ?? '';
$password = $data['password'] ?? '';
$rol = $data['rol'] ?? 'cliente';

if (!$nombre_usuario || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos obligatorios']);
    exit;
}

// Verificar si ya existe el nombre de usuario
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$nombre_usuario]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'El nombre de usuario ya está registrado']);
    exit;
}

// Generar hash de la contraseña
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar nuevo usuario
$stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, hash_contrasena, rol) VALUES (?, ?, ?)");

try {
    $stmt->execute([$nombre_usuario, $hash, $rol]);
    echo json_encode(['success' => true, 'message' => 'Usuario registrado correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al registrar: ' . $e->getMessage()]);
}
?>
