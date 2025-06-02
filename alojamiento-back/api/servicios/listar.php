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
    $stmt = $pdo->query("SELECT id, nombre_servicio, precio FROM servicios_adicionales ORDER BY nombre_servicio ASC");
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'servicios' => $servicios]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al listar servicios: ' . $e->getMessage()]);
}
?>
