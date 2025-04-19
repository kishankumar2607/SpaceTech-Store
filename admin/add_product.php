<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) header("Location: login.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $qty = $_POST['quantity'];
    $cat = $_POST['category'];
    $added_by = $_SESSION['admin_id'];

    // Image upload
    $image = $_FILES['image']['name'];
    $target = "../uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $pdo->prepare("INSERT INTO products (name, description, category, price, quantity, image, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $desc, $cat, $price, $qty, $image, $added_by]);

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Product - SpaceTech</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <main class="add-product-main">
        <h2>Add New Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <input name="name" placeholder="Product Name" required>
            <textarea name="description" placeholder="Description" required></textarea>

            <select name="category" required>
                <option value="">Select Category</option>
                <option value="laptop">Laptop</option>
                <option value="phone">Phone</option>
                <option value="accessory">Accessory</option>
                <option value="tablet">Tablet</option>
                <option value="audio">Audio</option>
            </select>

            <input name="price" type="number" step="0.01" placeholder="Price" required>
            <input name="quantity" type="number" placeholder="Stock Quantity" required>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Add Product</button>
        </form>
    </main>

    <footer class="footer">
        &copy; <?= date("Y") ?> Group Project - SpaceTech. All rights reserved.
    </footer>
</body>

</html>