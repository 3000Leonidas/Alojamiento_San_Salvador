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
        ) AS imagen,
        d.id AS id_descuento_especial,
        d.nombre_descuento,
        d.porcentaje_descuento,
        d.fecha_inicio AS fecha_inicio_descuento,
        d.fecha_fin AS fecha_fin_descuento,
        CASE 
                WHEN d.id IS NOT NULL AND CURDATE() BETWEEN d.fecha_inicio AND d.fecha_fin
                THEN 1
                ELSE 0
            END AS tiene_descuento_activo
    FROM habitaciones h
    LEFT JOIN descuento_habitaciones dh ON dh.id_habitacion = h.id
    LEFT JOIN descuentos_especiales d ON d.id = dh.id_descuento
    ORDER BY h.numero_habitacion ASC
");


    // $stmt = $pdo->query("SELECT * FROM habitaciones ORDER BY numero_habitacion ASC");
    $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Cast explícito a entero
foreach ($habitaciones as &$habitacion) {
    $habitacion['disponible'] = (int) $habitacion['disponible'];
    $habitacion['id_descuento_especial'] = isset($habitacion['id_descuento_especial']) ? (int)$habitacion['id_descuento_especial'] : null;
    $habitacion['porcentaje_descuento'] = isset($habitacion['porcentaje_descuento']) ? (float)$habitacion['porcentaje_descuento'] : null;
if ($habitacion['tiene_descuento_activo']) {
            $habitacion['precio_con_descuento'] = round(
                $habitacion['precio_por_noche'] * (1 - $habitacion['porcentaje_descuento'] / 100),
                2
            );
        }
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

