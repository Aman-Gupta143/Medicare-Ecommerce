<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('medicines.php');
}

$medicineId = (int) ($_POST['medicine_id'] ?? 0);
$redirectPath = safeRedirectPath($_POST['redirect'] ?? null, 'medicines.php');

if ($medicineId <= 0) {
    setFlash('error', 'Invalid medicine selected.');
    redirect($redirectPath);
}

$statement = $pdo->prepare('SELECT id FROM medicines WHERE id = ?');
$statement->execute([$medicineId]);

if (!$statement->fetch()) {
    setFlash('error', 'Medicine not found.');
    redirect($redirectPath);
}

$cart = getCart();
$cart[$medicineId] = ($cart[$medicineId] ?? 0) + 1;
saveCart($cart);

setFlash('success', 'Item added to cart successfully.');
redirect($redirectPath);