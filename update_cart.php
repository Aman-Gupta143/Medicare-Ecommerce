<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

$medicineId = (int) ($_POST['medicine_id'] ?? 0);
$action = $_POST['action'] ?? '';
$cart = getCart();

if ($medicineId <= 0 || !isset($cart[$medicineId])) {
    setFlash('error', 'Cart item not found.');
    redirect('cart.php');
}

if ($action === 'increase') {
    $cart[$medicineId]++;
} elseif ($action === 'decrease') {
    $cart[$medicineId]--;
    if ($cart[$medicineId] <= 0) {
        unset($cart[$medicineId]);
    }
} elseif ($action === 'remove') {
    unset($cart[$medicineId]);
} else {
    setFlash('error', 'Invalid cart action.');
    redirect('cart.php');
}

saveCart($cart);
setFlash('success', 'Cart updated successfully.');
redirect('cart.php');