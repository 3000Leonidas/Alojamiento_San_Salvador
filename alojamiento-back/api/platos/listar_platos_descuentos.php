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

try {
    $fechaActual = date('Y-m-d');

    $sql = "
        SELECT 
            p.id, 
            p.nombre, 
            p.descripcion, 
            p.precio AS precio_original,
            COALESCE(dp.precio_promocional, p.precio) AS precio,
            CASE 
                WHEN dp.id IS NOT NULL 
                AND de.fecha_inicio <= :fecha 
                AND de.fecha_fin >= :fecha 
                THEN 1 
                ELSE 0 
            END AS promocion,
            p.imagen
        FROM platos p
        LEFT JOIN descuento_platos dp ON dp.id_plato = p.id
        LEFT JOIN descuentos_especiales de ON dp.id_descuento = de.id
            AND de.fecha_inicio <= :fecha AND de.fecha_fin >= :fecha
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':fecha', $fechaActual);
    $stmt->execute();

    $platos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'platos' => $platos
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener platos: ' . $e->getMessage()
    ]);
}
?>
