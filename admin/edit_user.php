<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) header("Location: login.php");

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, address=?, phone=?, role=? WHERE id=?");
    $stmt->execute([$name, $email, $address, $phone, $role, $id]);

    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit User - SpaceTech</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <main class="add-product-main">
        <h2>Edit User</h2>
        <form method="POST">
            <input name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            <input name="email" type="email" value="<?= $user['email'] ?>" required>
            <input name="address" value="<?= $user['address'] ?>">
            <input name="phone" value="<?= $user['phone'] ?>">
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <button type="submit">Update User</button>
        </form>
    </main>
    <footer class="footer">
        &copy; <?= date("Y") ?> Group Project - SpaceTech. All rights reserved.
    </footer>
</body>

</html>