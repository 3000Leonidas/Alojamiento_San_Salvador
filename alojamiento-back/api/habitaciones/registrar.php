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

// Manejar datos de formulario multipart (para imágenes)
$numero_habitacion = $_POST['numero_habitacion'] ?? null;
$tipo_habitacion = $_POST['tipo_habitacion'] ?? null;
$precio_por_noche = $_POST['precio_por_noche'] ?? null;
$esta_disponible = isset($_POST['esta_disponible']) ? 1 : 0;
$imagen = $_FILES['imagen'] ?? null;

// Validar campos requeridos
if (!$numero_habitacion || !$tipo_habitacion || !$precio_por_noche) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
    exit;
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // Insertar habitación
    $stmt = $pdo->prepare("
        INSERT INTO habitaciones 
        (numero_habitacion, tipo_habitacion, precio_por_noche, esta_disponible) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$numero_habitacion, $tipo_habitacion, $precio_por_noche, $esta_disponible]);
    $id_habitacion = $pdo->lastInsertId();

    // Procesar imagen si existe
    if ($imagen && $imagen['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $filename = 'habitacion_' . $id_habitacion . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($imagen['tmp_name'], $destination)) {
            // Guardar referencia de imagen en la base de datos
            $stmt = $pdo->prepare("
                INSERT INTO imagenes_habitaciones 
                (url_imagen, id_habitacion) 
                VALUES (?, ?)
            ");
            $stmt->execute(["uploads/$filename", $id_habitacion]);
        } else {
            throw new Exception('Error al mover el archivo subido');
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Habitación registrada correctamente',
        'id_habitacion' => $id_habitacion
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al registrar habitación: ' . $e->getMessage()]);
}
?>