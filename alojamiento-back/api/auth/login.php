<?php
// CABECERAS CORS: SIEMPRE PRIMERO
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

// RESPUESTA A PRE-FLIGHT
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// CONEXIÓN
require_once('../../config/db.php');

// CAPTURA Y VALIDACIÓN
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['nombre_usuario'] ?? '';
$password = $data['password'] ?? '';

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit;
}

// CONSULTA
$stmt = $pdo->prepare("SELECT id, nombre_usuario, hash_contrasena, rol FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// VALIDACIÓN DE CONTRASEÑA
if ($user && password_verify($password, $user['hash_contrasena'])) {
    unset($user['hash_contrasena']);
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales incorrectas']);
}
?>
