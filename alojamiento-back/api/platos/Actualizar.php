<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

try {
    // Manejar tanto FormData como JSON
    $inputData = $_POST;
    if (empty($inputData)) {
        $inputData = json_decode(file_get_contents("php://input"), true);
    }

    if (empty($inputData['id']) || empty($inputData['nombre']) || empty($inputData['descripcion']) || empty($inputData['precio'])) {
        throw new Exception('Faltan campos requeridos');
    }

    $id = $inputData['id'];
    $nombre = $inputData['nombre'];
    $descripcion = $inputData['descripcion'];
    $precio = $inputData['precio'];
    $categoria = $_POST['categoria'];
    $id_descuento = $_POST['id_descuento'] ?? null;

    // Si se envió una nueva imagen
    if (!empty($_FILES['imagen']['name'])) {
        $directorio = '../../uploads/platos/';
        if (!is_dir($directorio)) mkdir($directorio, 0777, true);

        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nuevoNombre = uniqid('plato_') . '.' . $extension;
        $rutaGuardada = $directorio . $nuevoNombre;
        $rutaBD = 'uploads/platos/' . $nuevoNombre;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaGuardada)) {
            throw new Exception('Error al guardar la imagen');
        }

        $sql = "UPDATE platos SET nombre = ?, descripcion = ?, precio = ?, imagen = ? , categoria = ? WHERE id = ?";
        $params = [$nombre, $descripcion, $precio, $categoria, $rutaBD, $id];
    } else {
        $sql = "UPDATE platos SET nombre = ?, descripcion = ?, precio = ?, categoria = ? WHERE id = ?";
        $params = [$nombre, $descripcion, $precio, $categoria, $id];
    }
    if ($id_descuento) {
    // Primero eliminar cualquier descuento existente
    $stmt = $pdo->prepare("DELETE FROM descuento_platos WHERE id_plato = ?");
    $stmt->execute([$id]);
    
    // Luego insertar el nuevo
    $stmt = $pdo->prepare("INSERT INTO descuento_platos (id_descuento, id_plato) VALUES (?, ?)");
    $stmt->execute([$id_descuento, $id]);
    } else {
        // Eliminar descuento si se quitó
        $stmt = $pdo->prepare("DELETE FROM descuento_platos WHERE id_plato = ?");
        $stmt->execute([$id]);}

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'Plato actualizado correctamente']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'received_data' => $inputData,
        'files' => $_FILES
    ]);
}