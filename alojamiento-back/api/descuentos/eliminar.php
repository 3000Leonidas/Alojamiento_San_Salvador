<?php
// Cabeceras para CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

// Manejar solicitud DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Obtener el ID del parámetro de consulta o del cuerpo JSON
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    // Si no está en GET, intentar leer del cuerpo JSON
    if (!$id) {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
    }

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de descuento requerido']);
        exit;
    }

    try {
        // Primero eliminar las relaciones en descuento_platos
        $stmt = $pdo->prepare("DELETE FROM descuento_platos WHERE id_descuento = ?");
        $stmt->execute([$id]);
        
        // Luego eliminar el descuento
        $stmt = $pdo->prepare("DELETE FROM descuentos_especiales WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Descuento eliminado correctamente']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Descuento no encontrado']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al eliminar descuento: ' . $e->getMessage()]);
    }
    exit;
}

// Si no es DELETE, devolver error
http_response_code(405);
echo json_encode(['error' => 'Método no permitido']);
?>