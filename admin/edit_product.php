<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) header("Location: login.php");

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $qty = $_POST['quantity'];
    $cat = $_POST['category'];

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "../uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, category=?, price=?, quantity=?, image=? WHERE id=?");
        $stmt->execute([$name, $desc, $cat, $price, $qty, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, category=?, price=?, quantity=? WHERE id=?");
        $stmt->execute([$name, $desc, $cat, $price, $qty, $id]);
    }

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Product - SpaceTech</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <main class="add-product-main">
        <h2>Edit Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <input name="name" value="<?= htmlspecialchars($product['name']) ?>" placeholder="Product Name" required>
            <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

            <select name="category" required>
                <option value="">Select Category</option>
                <option value="laptop" <?= $product['category'] === 'laptop' ? 'selected' : '' ?>>Laptop</option>
                <option value="phone" <?= $product['category'] === 'phone' ? 'selected' : '' ?>>Phone</option>
                <option value="accessory" <?= $product['category'] === 'accessory' ? 'selected' : '' ?>>Accessory</option>
                <option value="tablet" <?= $product['category'] === 'tablet' ? 'selected' : '' ?>>Tablet</option>
                <option value="audio" <?= $product['category'] === 'audio' ? 'selected' : '' ?>>Audio</option>
            </select>

            <input name="price" type="number" step="0.01" value="<?= $product['price'] ?>" required>
            <input name="quantity" type="number" value="<?= $product['quantity'] ?>" required>

            <img src="../uploads/<?= $product['image'] ?>" alt="Product Image" width="100">
            <input type="file" name="image" accept="image/*">

            <button type="submit">Update Product</button>
        </form>
    </main>

    <footer class="footer">
        &copy; <?= date("Y") ?> Group Project - SpaceTech. All rights reserved.
    </footer>
</body>

</html>