<?php
session_start();

if (isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = (int)$qty;
        }
    }
}

header("Location: cart.php");
exit;
