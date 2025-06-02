<?php
// CABECERAS CORS al inicio
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

// RESPUESTA preflight (para peticiones OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Conexión a la base de datos
require_once('../../config/db.php');

// Captura de entrada
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['nombre_usuario'] ?? '';

if (!$username) {
    http_response_code(400);
    echo json_encode(['error' => 'Falta el nombre de usuario']);
    exit;
}

// Consulta de rol
$stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(['rol' => $user['rol']]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado']);
}
?>
