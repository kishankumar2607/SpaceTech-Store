<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) header("Location: login.php");

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

header("Location: products.php");
exit;
