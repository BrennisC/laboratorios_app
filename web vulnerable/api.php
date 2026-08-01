<?php
require_once __DIR__ . '/db.php';

// VULN: A02/A05 - Overly permissive CORS and API inputs concatenated into SQL.
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$resource = $_GET['resource'] ?? 'products';
$id = $_GET['id'] ?? '';
$payload = file_get_contents('php://input');
$safePayload = str_replace("'", "''", $payload);
db()->exec("INSERT INTO api_events (endpoint, method, payload, ip_address, created_at) VALUES ('$resource', '{$_SERVER['REQUEST_METHOD']}', '$safePayload', '{$_SERVER['REMOTE_ADDR']}', CURRENT_TIMESTAMP)");

if ($resource === 'users') {
    $sql = $id !== '' ? "SELECT * FROM users WHERE id = $id" : 'SELECT * FROM users ORDER BY id ASC';
    echo json_encode(db()->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($resource === 'orders') {
    $userId = $_GET['user_id'] ?? current_user_id() ?? 1;
    echo json_encode(db()->query("SELECT * FROM orders WHERE user_id = $userId ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($resource === 'products') {
    $sql = $id !== '' ? "SELECT * FROM products WHERE id = $id" : 'SELECT * FROM products ORDER BY id ASC';
    echo json_encode(db()->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Unknown resource']);
