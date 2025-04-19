<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];
$products = [];
$total = 0;

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();

    foreach ($products as $product) {
        $subtotal = $product['price'] * $cart[$product['id']];
        $total += $subtotal;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $errors = [];
    
    // Validate payment method
    if (empty($_POST['payment_method'])) {
        $errors['payment_method'] = "Payment method is required";
    }
    
    // If no errors, proceed to checkout
    if (empty($errors)) {
        $_SESSION['checkout_details'] = [
            'payment_method' => $_POST['payment_method'],
            'shipping_address' => $_POST['shipping_address'] ?? $user['address']
        ];
        header("Location: checkout.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cart - SpaceTech</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        .checkout-section {
            margin-top: 40px;
            padding: 30px;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .checkout-section h3 {
            margin-top: 0;
            color: #2c3e50;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 25px;
        }
        
        .form-row {
            display: flex;
            margin-bottom: 20px;
            align-items: center;
        }
        
        .form-row label {
            flex: 0 0 150px;
            font-weight: 600;
            color: #555;
        }
        
        .form-row .form-field {
            flex: 1;
        }
        
        .form-row input, 
        .form-row select, 
        .form-row textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        
        .form-row input:focus, 
        .form-row select:focus, 
        .form-row textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .form-row textarea {
            height: 100px;
            resize: vertical;
        }
        
        .payment-options {
            margin-top: 10px;
        }
        
        .payment-option {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .payment-option:last-child {
            border-bottom: none;
        }
        
        .payment-option label {
            margin-left: 10px;
            font-weight: normal;
            cursor: pointer;
        }
        
        .error {
            color: #e74c3c;
            font-size: 0.9em;
            margin-top: 5px;
            margin-left: 150px;
        }
        
        
        
        @media (min-width: 768px) {
            .checkout-columns {
                display: flex;
                gap: 40px;
            }
            
            .checkout-column {
                flex: 1;
            }
            
            .place-order-btn {
                margin-right: 0;
            }
        }
        
        @media (max-width: 767px) {
            .form-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .form-row label {
                margin-bottom: 8px;
                flex: 1;
            }
            
            .form-row .form-field {
                width: 100%;
            }
            
            .error {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <header class="admin-nav">
        <h1><a href="index.php">SpaceTech</a></h1>
        <ul>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="admin/login.php">Admin Dashboard</a></li>
        </ul>
    </header>

    <main class="cart-main">
        <h2>Your Shopping Cart</h2>
        <?php if ($products): ?>
            <form method="POST" action="cart_update.php" class="cart-form">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <td>$<?= number_format($p['price'], 2) ?></td>
                                    <td>
                                        <input type="number" name="quantities[<?= $p['id'] ?>]" value="<?= $cart[$p['id']] ?>" min="1" max="<?= $p['quantity'] ?>">
                                    </td>
                                    <td>$<?= number_format($p['price'] * $cart[$p['id']], 2) ?></td>
                                    <td><a href="cart_remove.php?id=<?= $p['id'] ?>" class="btn-delete">❌</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <h3>Total: $<?= number_format($total, 2) ?></h3>
                    <button type="submit" class="btn">Update Cart</button>
                </div>
            </form>
            
            <!-- Checkout Section -->
            <form method="POST" action="" class="checkout-section">
                <h3>Checkout Details</h3>
                
                <div class="checkout-columns">
                    <div class="checkout-column">
                        <h4>User Information</h4>
                        <div class="form-row">
                            <label>Name</label>
                            <div class="form-field">
                                <input type="text" value="<?= htmlspecialchars($user['name']) ?>" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Email</label>
                            <div class="form-field">
                                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Phone</label>
                            <div class="form-field">
                                <input type="text" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
                            </div>
                        </div>
                        
                        <h4>Shipping Address</h4>
                        <div class="form-row">
                            <label for="shipping_address">Address</label>
                            <div class="form-field">
                                <textarea id="shipping_address" name="shipping_address"><?= htmlspecialchars($user['address']) ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="checkout-column">
                        <h4>Payment Method</h4>
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" id="credit_card" name="payment_method" value="credit_card" <?= isset($_POST['payment_method']) && $_POST['payment_method'] === 'credit_card' ? 'checked' : '' ?> required>
                                <label for="credit_card">Credit Card</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="paypal" name="payment_method" value="paypal" <?= isset($_POST['payment_method']) && $_POST['payment_method'] === 'paypal' ? 'checked' : '' ?>>
                                <label for="paypal">PayPal</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" <?= isset($_POST['payment_method']) && $_POST['payment_method'] === 'bank_transfer' ? 'checked' : '' ?>>
                                <label for="bank_transfer">Bank Transfer</label>
                            </div>
                        </div>
                        <?php if (isset($errors['payment_method'])): ?>
                            <div class="error"><?= $errors['payment_method'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <button type="submit" name="place_order" class="place-order-btn">Place Order</button>
            </form>
        <?php else: ?>
            <p style="color: red; font-size: 1.2rem">Your cart is empty. <br>
                <a href="index.php"><button style="margin-top: 30px;">Start shopping </button></a>
            </p>
        <?php endif; ?>
    </main>

    <footer class="footer">
        &copy; <?= date("Y") ?> SpaceTech Store. All rights reserved.
    </footer>
</body>

</html>