<?php
// Cabeceras para CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$nombre_usuario = $data['nombre_usuario'] ?? null;
$rol = $data['rol'] ?? null;

if (!$id || !$nombre_usuario || !$rol) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre_usuario = ?, rol = ? WHERE id = ?");
    $stmt->execute([$nombre_usuario, $rol, $id]);

    echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar: ' . $e->getMessage()]);
}
?>