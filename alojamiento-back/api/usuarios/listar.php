<?php
// Cabeceras para CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

try {
    $stmt = $pdo->query("SELECT id, nombre_usuario, rol, creado_en FROM usuarios ORDER BY creado_en DESC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'usuarios' => $usuarios]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al listar usuarios: ' . $e->getMessage()]);
}
?>
