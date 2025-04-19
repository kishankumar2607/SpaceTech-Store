<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Empty Cart - SpaceTech</title>
        <link rel='stylesheet' href='assets/styles.css'>
    </head>
    <body>
        <header class='admin-nav'>
            <h1><a href='index.php'>SpaceTech</a></h1>
            <ul>
                <li><a href='cart.php'>Cart</a></li>
                <li><a href='login.php'>Login</a></li>
                <li><a href='admin/login.php'>Admin Dashboard</a></li>
            </ul>
        </header>
        <main class='form-page'>
            <h2>Your cart is empty</h2>
            <p><a href='index.php' class='btn'>Shop Now</a></p>
        </main>
        <footer class='footer'>&copy; " . date('Y') . " SpaceTech. All rights reserved.</footer>
    </body>
    </html>
    ";
    exit;
}

$ids = implode(',', array_keys($cart));
$stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
$products = $stmt->fetchAll();

$total = 0;
foreach ($products as $product) {
    $total += $product['price'] * $cart[$product['id']];
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $total]);
    $order_id = $pdo->lastInsertId();

    foreach ($products as $product) {
        $qty = $cart[$product['id']];
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $product['id'], $qty, $product['price']]);

        $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
        $stmt->execute([$qty, $product['id']]);
    }

    $pdo->commit();

    // Clear cart and redirect to PDF invoice
    $_SESSION['cart'] = [];
    header("Location: invoice.php?order_id=$order_id");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Error - SpaceTech</title>
        <link rel='stylesheet' href='assets/styles.css'>
    </head>
    <body>
        <main class='form-page'>
            <h2 style='color:red;'>Something went wrong during checkout</h2>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
            <p><a href='cart.php' class='btn'>Return to Cart</a></p>
        </main>
    </body>
    </html>
    ";
}
