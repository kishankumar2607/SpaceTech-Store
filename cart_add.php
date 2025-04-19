<?php
session_start();
require_once 'includes/db.php';

$product_id = $_POST['product_id'];
$quantity = max(1, (int)$_POST['quantity']);

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

header("Location: cart.php");
exit;
