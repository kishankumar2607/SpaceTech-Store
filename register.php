<?php
session_start();
require_once 'includes/db.php';

class UserRegistration {
    private $pdo;
    private $name;
    private $email;
    private $password;
    private $address;
    private $phone;
    private $errors = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->initializeFromPost();
    }

    private function initializeFromPost() {
        $this->name = $_POST['name'] ?? '';
        $this->email = $_POST['email'] ?? '';
        $this->password = $_POST['password'] ?? '';
        $this->address = $_POST['address'] ?? '';
        $this->phone = $_POST['phone'] ?? '';
    }

    public function validate() {
        $this->errors = [];

        if (!$this->name || !$this->email || !$this->password || !$this->address || !$this->phone) {
            $this->errors[] = "All fields are required.";
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email format.";
        }

        if (strlen($this->password) < 6) {
            $this->errors[] = "Password must be at least 6 characters.";
        }

        if ($this->isEmailRegistered()) {
            $this->errors[] = "Email already registered.";
        }

        return empty($this->errors);
    }

    private function isEmailRegistered() {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$this->email]);
        return (bool)$stmt->fetch();
    }

    public function registerUser() {
        if (!$this->validate()) {
            return false;
        }

        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name, email, password, address, phone, role) 
             VALUES (?, ?, ?, ?, ?, 'customer')"
        );
        $stmt->execute([
            $this->name, 
            $this->email, 
            $hashedPassword, 
            $this->address, 
            $this->phone
        ]);

        $this->setUserSession($this->pdo->lastInsertId());
        return true;
    }

    private function setUserSession($userId) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $this->name;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getPhone() {
        return $this->phone;
    }
}

// Initialize variables
$errors = [];
$registration = null;

// Process form if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration = new UserRegistration($pdo);
    if ($registration->registerUser()) {
        header("Location: checkout.php");
        exit;
    }
    $errors = $registration->getErrors();
} else {
    // Create empty registration object for GET requests
    $registration = new UserRegistration($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SpaceTech</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <header class="admin-nav">
        <h1><a href="index.php">SpaceTech</a></h1>
    </header>

    <main class="form-page">
        <h2>Customer Registration</h2>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $e): ?>
                <p class="error"><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="POST" class="form-card">
            <input name="name" placeholder="Full Name" value="<?= htmlspecialchars($registration->getName()) ?>" required>
            <input name="email" type="email" placeholder="Email" value="<?= htmlspecialchars($registration->getEmail()) ?>" required>
            <input name="password" type="password" placeholder="Password" required>
            <input name="address" placeholder="Address" value="<?= htmlspecialchars($registration->getAddress()) ?>" required>
            <input name="phone" placeholder="Phone" value="<?= htmlspecialchars($registration->getPhone()) ?>" required>
            <button type="submit">Register</button>
        </form>

        <p style="margin-top: 1rem;">Already have an account? <a href="login.php">Login here</a>.</p>
    </main>

    <footer class="footer">
        &copy; <?= date("Y") ?> SpaceTech Store. All rights reserved.
    </footer>
</body>

</html>