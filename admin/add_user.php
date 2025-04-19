<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) header("Location: login.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, address, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $address, $phone, $role]);

    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add User - SpaceTech</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <main class="add-product-main">
        <h2>Add New User</h2>
        <form method="POST">
            <input name="name" placeholder="Full Name" required>
            <input name="email" type="email" placeholder="Email" required>
            <input name="password" type="password" placeholder="Password" required>
            <input name="address" placeholder="Address">
            <input name="phone" placeholder="Phone">
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">Add User</button>
        </form>
    </main>
    <footer class="footer">
        &copy; <?= date("Y") ?> Group Project - SpaceTech. All rights reserved.
    </footer>
</body>

</html>