<?php
session_start();
require_once '../includes/db.php';

class AdminLogin {
    private $pdo;
    private $email;
    private $password;
    private $error;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->initializeFromPost();
    }

    private function initializeFromPost() {
        $this->email = $_POST['email'] ?? '';
        $this->password = $_POST['password'] ?? '';
    }

    public function authenticate() {
        $admin = $this->getAdminByEmail();

        if ($admin && password_verify($this->password, $admin['password'])) {
            $this->setAdminSession($admin);
            return true;
        }

        $this->error = "Invalid credentials.";
        return false;
    }

    private function getAdminByEmail() {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$this->email]);
        return $stmt->fetch();
    }

    private function setAdminSession($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
    }

    public function getError() {
        return $this->error ?? null;
    }

    public function getEmail() {
        return $this->email;
    }
}

// Process login if POST request
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminLogin = new AdminLogin($pdo);
    if ($adminLogin->authenticate()) {
        header('Location: dashboard.php');
        exit;
    }
    $error = $adminLogin->getError();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Login - SpaceTech</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body class="login-page">
    <form method="POST" class="form-card">
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
            <p class='error'><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <input type="email" name="email" placeholder="Email" value="<?= isset($adminLogin) ? htmlspecialchars($adminLogin->getEmail()) : '' ?>" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <button style="background-color: lightgrey; color: black" type="button" onclick="window.location.href='../login.php'">Back to User Login</button>
    </form>
    <br>
</body>

</html>