<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) header("Location: login.php");

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Users - SpaceTech</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <main class="products-main">
        <h2>Registered Users</h2>
        <a href="add_user.php" class="btn">Add New User</a>
        <div class="table-wrapper">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td><?= $u['email'] ?></td>
                        <td><?= $u['phone'] ?></td>
                        <td><?= $u['role'] ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $u['id'] ?>">Edit</a> |
                            <a href="delete_user.php?id=<?= $u['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
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