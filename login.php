<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pw = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pw, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SpaceTech</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <header class="admin-nav">
        <h1><a href="index.php">SpaceTech</a></h1>

    </header>

    <main class="form-page">
        <h2>Customer Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="form-card">
            <input name="email" type="email" placeholder="Email" required>
            <input name="password" type="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p style="margin-top: 1rem;">Don't have an account? <a href="register.php">Register here</a>.</p>
    </main>

    <footer class="footer">
        &copy; <?= date("Y") ?> SpaceTech Store. All rights reserved.
    </footer>
</body>

</html>