<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once('../../config/db.php');

try {
  $stmt = $pdo->query("SELECT * FROM huespedes ORDER BY id DESC");
  $huespedes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['success' => true, 'huespedes' => $huespedes]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
