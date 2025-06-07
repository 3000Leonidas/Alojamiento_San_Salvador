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
$id = $data['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID del plato no proporcionado']);
    exit;
}

try {
    // Buscar imagen actual para eliminar archivo del servidor
    $stmt = $pdo->prepare("SELECT imagen FROM platos WHERE id = ?");
    $stmt->execute([$id]);
    $plato = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$plato) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Plato no encontrado']);
        exit;
    }

    // Eliminar plato
    $stmt = $pdo->prepare("DELETE FROM platos WHERE id = ?");
    $stmt->execute([$id]);

    // Eliminar imagen del servidor si existe
    if ($plato['imagen']) {
        $ruta = '../../' . $plato['imagen'];
        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Plato eliminado correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al eliminar plato: ' . $e->getMessage()]);
}
