<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

// try {
try {
    $stmt = $pdo->query("
        SELECT 
            h.id,
            h.precio_por_noche,
            h.tipo_habitacion,
            h.esta_disponible AS disponible,
            h.numero_habitacion,
            (
                SELECT url_imagen 
                FROM imagenes_habitaciones 
                WHERE id_habitacion = h.id 
                LIMIT 1
            ) AS imagen
        FROM habitaciones h
        ORDER BY h.numero_habitacion ASC
    ");

    // $stmt = $pdo->query("SELECT * FROM habitaciones ORDER BY numero_habitacion ASC");
    $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Cast explícito a entero
foreach ($habitaciones as &$habitacion) {
    $habitacion['disponible'] = (int) $habitacion['disponible'];
}

echo json_encode([
    'success' => true,
    'habitaciones' => $habitaciones
]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener habitaciones: ' . $e->getMessage()
    ]);
}
?>

