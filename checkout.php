<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Checkout';
$currentPage = 'checkout';
$cartData = getCartDetails($pdo);
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$user) {
        setFlash('error', 'Please sign in or sign up before placing your order.');
        redirect('auth.php');
    }

    if ($cartData['items'] === []) {
        setFlash('error', 'Your cart is empty.');
        redirect('cart.php');
    }

    $addressType = trim($_POST['address_type'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? '');

    if ($addressType === '' || $address === '' || $paymentMethod === '') {
        setFlash('error', 'Please fill all checkout details.');
    } else {
        try {
            $pdo->beginTransaction();

            $orderStatement = $pdo->prepare(
                'INSERT INTO customer_orders (user_id, address_type, address, payment_method, total_amount, status) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $orderStatement->execute([
                (int) $user['id'],
                $addressType,
                $address,
                $paymentMethod,
                $cartData['total'],
                'Pending',
            ]);

            $orderId = (int) $pdo->lastInsertId();
            $itemStatement = $pdo->prepare(
                'INSERT INTO order_items (order_id, medicine_id, quantity, price) VALUES (?, ?, ?, ?)'
            );

            foreach ($cartData['items'] as $item) {
                $s=$pdo->prepare('SELECT stock FROM medicines WHERE id=?');$s->execute([$item['id']]);$stock=(int)$s->fetchColumn(); if($stock < $item['quantity']){ throw new Exception('Out of stock'); }
                $pdo->prepare('UPDATE medicines SET stock=stock-? WHERE id=?')->execute([$item['quantity'],$item['id']]);
                $itemStatement->execute([
                    $orderId,
                    $item['id'],
                    $item['quantity'],
                    $item['price'],
                ]);
            }

            $pdo->commit();

            saveCart([]);
            setFlash('success', 'Order placed successfully. Order ID: #' . $orderId . '. Our Agent will contact you within 24-72 hours');
            redirect('index.php');
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            setFlash('error', 'Order could not be placed. Please try again.');
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main class="page-shell">
    <section class="section-head">
        <div>
            <h1>Checkout</h1>
            <p class="muted">Sign in before placing an order for secure customer details and database storage.</p>
        </div>
    </section>

    <section class="two-column">
        <div class="panel form-panel">
            <?php if (!$user): ?>
                <div class="empty-state">Please sign in first, then come back to checkout.</div>
                <br>
                <a class="btn" href="auth.php">Go To Sign In</a>
            <?php elseif ($cartData['items'] === []): ?>
                <div class="empty-state">No items in cart.</div>
                <br>
                <a class="btn" href="medicines.php">Shop Medicines</a>
            <?php else: ?>
                <form method="post" class="stack">
                    <div class="field-group">
                        <label for="address_type">Address Type</label>
                        <select id="address_type" name="address_type">
                            <option value="">Select Address Type</option>
                            <option value="Home Address" <?= ($_POST['address_type'] ?? '') === 'Home Address' ? 'selected' : ''; ?>>Home Address</option>
                            <option value="Office Address" <?= ($_POST['address_type'] ?? '') === 'Office Address' ? 'selected' : ''; ?>>Office Address</option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="address">Full Address</label>
                        <textarea id="address" name="address" placeholder="Enter full address"><?= e($_POST['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="field-group">
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method">
                            <option value="">Select Payment Method</option>
                            <option value="UPI" <?= ($_POST['payment_method'] ?? '') === 'UPI' ? 'selected' : ''; ?>>UPI</option>
                            <option value="COD" <?= ($_POST['payment_method'] ?? '') === 'COD' ? 'selected' : ''; ?>>COD</option>
                            <option value="Card" <?= ($_POST['payment_method'] ?? '') === 'Card' ? 'selected' : ''; ?>>Card</option>
                        </select>
                    </div>

                    <button class="btn" type="submit">Place Order</button>
                </form>
            <?php endif; ?>
        </div>

        <aside class="panel summary-card">
            <h3>Order Summary</h3>
            <p class="muted">
                <?= $user ? 'Signed in as ' . e($user['name']) . ' (' . e($user['email']) . ')' : 'Please sign in to continue with checkout.'; ?>
            </p>
            <br>

            <?php if ($cartData['items'] === []): ?>
                <div class="empty-state">No items in cart.</div>
            <?php else: ?>
                <div class="table-like">
                    <?php foreach ($cartData['items'] as $item): ?>
                        <div class="summary-line">
                            <span><?= e($item['name']); ?> x <?= $item['quantity']; ?></span>
                            <strong><?= formatPrice($item['subtotal']); ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
                <br>
                <strong>Total: <?= formatPrice($cartData['total']); ?></strong>
            <?php endif; ?>
        </aside>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>