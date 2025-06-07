<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

try {
    // Verificar si es multipart/form-data (con archivo)
    if (isset($_FILES['imagen'])) {
        $nombre = $_POST['nombre'] ?? null;
        $descripcion = $_POST['descripcion'] ?? null;
        $precio = $_POST['precio'] ?? null;
    } else {
        // Si no viene archivo, intentar leer como JSON
        $data = json_decode(file_get_contents("php://input"), true);
        $nombre = $data['nombre'] ?? null;
        $descripcion = $data['descripcion'] ?? null;
        $precio = $data['precio'] ?? null;
    }

    // Validación más robusta
    if (empty($nombre) || empty($descripcion) || empty($precio)) {
        throw new Exception('Faltan campos obligatorios: ' . 
                         (!empty($nombre) ? '' : 'nombre ') . 
                         (!empty($descripcion) ? '' : 'descripcion ') . 
                         (!empty($precio) ? '' : 'precio'));
    }

    $rutaBD = null;

    // Procesar imagen si existe
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = $_FILES['imagen'];
        
        // Validar tipo de imagen
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imagen['type'], $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido. Solo se aceptan JPEG, PNG o GIF');
        }

        $directorio = '../../uploads/platos/';
        if (!is_dir($directorio)) {
            if (!mkdir($directorio, 0777, true)) {
                throw new Exception('No se pudo crear el directorio para guardar imágenes');
            }
        }

        $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $nuevoNombre = 'plato_' . time() . '_' . uniqid() . '.' . $extension;
        $rutaGuardada = $directorio . $nuevoNombre;
        $rutaBD = 'uploads/platos/' . $nuevoNombre;

        if (!move_uploaded_file($imagen['tmp_name'], $rutaGuardada)) {
            throw new Exception('No se pudo guardar la imagen en el servidor');
        }
    }

    // Insertar en la base de datos
    $stmt = $pdo->prepare("INSERT INTO platos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)");
    $success = $stmt->execute([$nombre, $descripcion, $precio, $rutaBD]);

    if (!$success) {
        throw new Exception('Error al insertar en la base de datos');
    }
    if ($id_descuento) {
        $id_plato = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO descuento_platos (id_descuento, id_plato) VALUES (?, ?)");
        $stmt->execute([$id_descuento, $id_plato]);
    }
    // Respuesta exitosa
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Plato registrado correctamente',
        'data' => [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'categoria' => $categoria = $_POST['categoria'],
            'id_descuento' => $id_descuento ?? null,
            'imagen' => $rutaBD
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'post_data' => $_POST,
            'files_data' => $_FILES,
            'input_data' => file_get_contents("php://input")
        ]
    ]);
}