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

$id = $_POST['id'] ?? null;
$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$precio = $_POST['precio'] ?? 0;

if (!$nombre || !$descripcion || !$precio) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios.']);
    exit;
}

try {
    // Manejar imagen si se sube
    $imagenPath = null;
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $targetDir = '../../uploads/platos/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
            $imagenPath = 'uploads/platos/' . $fileName;
        } else {
            throw new Exception('Error al subir la imagen.');
        }
    }

    if ($id) {
        // Actualizar
        $sql = "UPDATE platos SET nombre = ?, descripcion = ?, precio = ?";
        $params = [$nombre, $descripcion, $precio];

        if ($imagenPath) {
            $sql .= ", imagen = ?";
            $params[] = $imagenPath;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } else {
        // Crear nuevo
        $stmt = $pdo->prepare("INSERT INTO platos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $precio, $imagenPath]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
