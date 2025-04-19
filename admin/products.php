<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) header("Location: login.php");

$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Products - SpaceTech</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <main class="products-main">
        <h2>Manage Products</h2>
        <a href="add_product.php" class="btn">Add New Product</a>
        <div class="table-wrapper">
            <table>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><img src="../uploads/<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['name']) ?>" width="50"></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td>$<?= number_format($p['price'], 2) ?></td>
                        <td><?= $p['quantity'] ?></td>
                        <td>
                            <a href="edit_product.php?id=<?= $p['id'] ?>">Edit</a> |
                            <a href="delete_product.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </main>

    <footer class="footer">
        &copy; <?= date("Y") ?> Group Project - SpaceTech. All rights reserved.
    </footer>
</body>

</html>