<?php
session_start();
require_once 'includes/db.php';

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM products WHERE quantity > 0";
$params = [];

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if ($search) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shop Electronics - SpaceTech</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <header class="admin-nav">
        <h1><a href="index.php">SpaceTech</a></h1>
        <ul>
            <li><a href="cart.php">Cart</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
            <li><a href="admin/login.php">Admin Dashboard</a></li>
        </ul>
    </header>

    <main class="shop-main">
        <h2>Explore Our Electronics</h2>

        <form method="GET" class="filter-form" aria-label="Product Filters">
            <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>" aria-label="Search by product name">
            <select name="category" aria-label="Select category">
                <option value="">All Categories</option>
                <option value="laptop" <?= $category === 'laptop' ? 'selected' : '' ?>>Laptop</option>
                <option value="phone" <?= $category === 'phone' ? 'selected' : '' ?>>Phone</option>
                <option value="accessory" <?= $category === 'accessory' ? 'selected' : '' ?>>Accessory</option>
                <option value="tablet" <?= $category === 'tablet' ? 'selected' : '' ?>>Tablet</option>
                <option value="audio" <?= $category === 'audio' ? 'selected' : '' ?>>Audio</option>
            </select>
            <button type="submit">Apply</button>
        </form>

        <?php if (count($products) === 0): ?>
            <p>No products found for your filter.</p>
        <?php endif; ?>

        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <img src="uploads/<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['name']) ?> image" loading="lazy">
                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                    <p>$<?= number_format($p['price'], 2) ?></p>
                    <form method="POST" action="cart_add.php" aria-label="Add <?= htmlspecialchars($p['name']) ?> to cart">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <input type="number" name="quantity" min="1" max="<?= $p['quantity'] ?>" value="1" aria-label="Quantity">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="footer">
        &copy; <?= date("Y") ?> SpaceTech Store. All rights reserved.
    </footer>
</body>

</html>