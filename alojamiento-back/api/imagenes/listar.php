<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

try {
    $id_habitacion = $_GET['id_habitacion'] ?? null;

    if ($id_habitacion) {
        $stmt = $pdo->prepare("SELECT id, url_imagen, id_habitacion FROM imagenes_habitaciones WHERE id_habitacion = ?");
        $stmt->execute([$id_habitacion]);
    } else {
        $stmt = $pdo->query("SELECT id, url_imagen, id_habitacion FROM imagenes_habitaciones ORDER BY id_habitacion");
    }

    $imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'imagenes' => $imagenes]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al listar imágenes: ' . $e->getMessage()]);
}
?>
