<?php
// Cabeceras para CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
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
    echo json_encode(['error' => 'ID de imagen requerido']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT url_imagen FROM imagenes_habitaciones WHERE id = ?");
    $stmt->execute([$id]);
    $imagen = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($imagen) {
        $filePath = '../../' . $imagen['url_imagen'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $stmt = $pdo->prepare("DELETE FROM imagenes_habitaciones WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Imagen eliminada correctamente']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Imagen no encontrada']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al eliminar imagen: ' . $e->getMessage()]);
}
?>
