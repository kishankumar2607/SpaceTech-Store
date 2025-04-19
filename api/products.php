<?php
header("Content-Type: application/json");
require_once '../includes/db.php';

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT id, name, description, category, price, image FROM products WHERE quantity > 0";
$params = [];

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if (!empty($search)) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => "success",
    "count" => count($products),
    "products" => $products
]);
