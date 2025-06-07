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
    $query = "
        SELECT 
            p.id,
            p.nombre,
            p.descripcion,
            p.precio AS precio_original,
            p.imagen,
            p.categoria,
            d.id AS descuento_id,
            d.nombre_descuento,
            d.porcentaje_descuento AS porcentaje_descuento,
            d.fecha_inicio,
            d.fecha_fin,
            CASE 
                WHEN d.id IS NOT NULL AND 
                     (d.fecha_inicio <= CURDATE()) AND 
                     (d.fecha_fin >= CURDATE())
                THEN 1
                ELSE 0
            END AS tiene_descuento
        FROM 
            platos p
        LEFT JOIN 
            descuento_platos dp ON p.id = dp.id_plato
        LEFT JOIN 
            descuentos_especiales d ON dp.id_descuento = d.id
        ORDER BY
            p.nombre ASC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $platos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $plato = [
            'id' => (int)$row['id'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'precio_original' => (float)$row['precio_original'],
            'imagen' => $row['imagen'],
            'categoria' => $row['categoria'],
            'tiene_descuento' => (bool)$row['tiene_descuento']
        ];

        if ($row['tiene_descuento'] && $row['descuento_id']) {
            $plato['descuento'] = [
                'id' => (int)$row['descuento_id'],
                'nombre_descuento' => $row['nombre_descuento'],
                'porcentaje_descuento' => (float)$row['porcentaje_descuento'],
                'fecha_inicio' => $row['fecha_inicio'],
                'fecha_fin' => $row['fecha_fin']
            ];
            $plato['precio'] = round(
                $plato['precio_original'] * (1 - $plato['descuento']['porcentaje_descuento'] / 100), 
                2
            );
        } else {
            $plato['precio'] = $plato['precio_original'];
            $plato['descuento'] = null;
        }

        $platos[] = $plato;
    }

    echo json_encode([
    'success' => true,
    'platos' => $platos,
    'count' => count($platos),
    'current_date' => date('Y-m-d'),
    'debug_info' => [
        'platos_con_descuento' => array_filter($platos, function($p) { return $p['tiene_descuento']; }),
        'total_descuentos_activos' => count(array_filter($platos, function($p) { return $p['tiene_descuento']; }))
    ]
]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>