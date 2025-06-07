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
    $sql = "
        SELECT 
            h.id,
            h.tipo_habitacion,
            h.numero_habitacion,
            h.precio_por_noche AS precio_original,
            d.nombre_descuento,
            d.porcentaje_descuento,
            (h.precio_por_noche - (h.precio_por_noche * d.porcentaje_descuento / 100)) AS precio_con_descuento
        FROM habitaciones h
        LEFT JOIN descuento_habitaciones dh ON h.id = dh.id_habitacion
        LEFT JOIN descuentos_especiales d ON dh.id_descuento = d.id
            AND CURDATE() BETWEEN d.fecha_inicio AND d.fecha_fin
    ";

    $stmt = $pdo->query($sql);
    $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($habitaciones as &$hab) {
        $hab['tiene_descuento'] = $hab['porcentaje_descuento'] !== null;
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
