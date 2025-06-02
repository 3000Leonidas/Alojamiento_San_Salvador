<?php
require_once('../config/db.php');

try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo "❌ Error ejecutando consulta: " . $e->getMessage();
}
?>
