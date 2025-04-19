<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Dashboard - SpaceTech</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <nav class="admin-nav">
        <h1>SpaceTech</h1>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="dashboard-main">
        <h2>Welcome, <?= $_SESSION['admin_name'] ?>!</h2>
        <p>Use the navigation menu above to manage your products and users.</p>
    </main>

    <footer class="footer">
        &copy; <?= date("Y") ?> Group Project - SpaceTech. All rights reserved.
    </footer>
</body>

</html>