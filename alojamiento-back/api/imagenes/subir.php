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

if (!isset($_POST['id_habitacion']) || !isset($_FILES['imagen'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos o archivo de imagen']);
    exit;
}

$id_habitacion = $_POST['id_habitacion'];
$imagen = $_FILES['imagen'];

$uploadDir = '../../uploads/';
$filename = uniqid('img_') . '_' . basename($imagen['name']);
$uploadPath = $uploadDir . $filename;

if (move_uploaded_file($imagen['tmp_name'], $uploadPath)) {
    $relativePath = 'uploads/' . $filename;

    $stmt = $pdo->prepare("INSERT INTO imagenes_habitaciones (url_imagen, id_habitacion) VALUES (?, ?)");
    $stmt->execute([$relativePath, $id_habitacion]);

    echo json_encode(['success' => true, 'message' => 'Imagen subida con éxito', 'url_imagen' => $relativePath]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al mover el archivo subido']);
}
?>
