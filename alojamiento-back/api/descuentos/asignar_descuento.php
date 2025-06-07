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

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $id_habitacion = $input['id_habitacion'] ?? null;
    $id_descuento = $input['id_descuento'] ?? null;

    if (!$id_habitacion) {
        throw new Exception("ID de habitación requerido");
    }

    // Buscar si ya existe una asignación
    $stmt = $pdo->prepare("SELECT * FROM descuento_habitaciones WHERE id_habitacion = ?");
    $stmt->execute([$id_habitacion]);
    $existe = $stmt->fetch();

    if ($existe) {
        if ($id_descuento) {
            // Actualizar el descuento asignado
            $update = $pdo->prepare("UPDATE descuento_habitaciones SET id_descuento = ? WHERE id_habitacion = ?");
            $update->execute([$id_descuento, $id_habitacion]);
        } else {
            // Quitar el descuento (eliminar la fila)
            $delete = $pdo->prepare("DELETE FROM descuento_habitaciones WHERE id_habitacion = ?");
            $delete->execute([$id_habitacion]);
        }
    } else {
        if ($id_descuento) {
            // Insertar nueva relación
            $insert = $pdo->prepare("INSERT INTO descuento_habitaciones (id_descuento, id_habitacion) VALUES (?, ?)");
            $insert->execute([$id_descuento, $id_habitacion]);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Descuento actualizado correctamente'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
