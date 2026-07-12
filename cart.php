<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Your Cart';
$currentPage = 'cart';

$cartData = getCartDetails($pdo);

include __DIR__ . '/includes/header.php';
?>

<main class="page-shell">
    <section class="section-head">
        <div>
            <h1>Your Cart</h1>
            <p class="muted">Increase or decrease quantity for the same product directly here.</p>
        </div>
    </section>

    <?php if ($cartData['items'] === []): ?>

        <div class="empty-state">Your cart is empty.</div>

    <?php else: ?>

        <section class="stack">

            <?php foreach ($cartData['items'] as $item): ?>

                <?php
                $qty = (int)$item['quantity'];
                $stock = (int)$item['stock'];
                ?>

                <article class="cart-item">
                    <div class="cart-head">

                        <div>
                            <strong><?= e($item['name']); ?></strong>

                            <div class="cart-meta">
                                Category: <?= e($item['category']); ?>
                            </div>

                            <div class="cart-meta">
                                Price: <?= formatPrice($item['price']); ?>
                            </div>

                            <div class="cart-meta">
                                Stock Available: <?= $stock; ?>
                            </div>

                            <div class="cart-meta">
                                Subtotal: <?= formatPrice($item['subtotal']); ?>
                            </div>

                            <?php if ($qty >= $stock): ?>
                                <div class="cart-meta" style="color:red; font-weight:bold;">
                                    Out of Stock
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="cart-controls">

                            <!-- Decrease -->
                            <form method="post" action="update_cart.php">
                                <input type="hidden" name="medicine_id" value="<?= $item['id']; ?>">
                                <input type="hidden" name="action" value="decrease">
                                <button class="qty-btn" type="submit">-</button>
                            </form>

                            <!-- Quantity -->
                            <span class="qty-value"><?= $qty; ?></span>

                            <!-- Increase -->
                            <form method="post" action="update_cart.php">
                                <input type="hidden" name="medicine_id" value="<?= $item['id']; ?>">
                                <input type="hidden" name="action" value="increase">

                                <button
                                    class="qty-btn"
                                    type="submit"
                                    <?= $qty >= $stock ? 'disabled' : ''; ?>
                                >
                                    +
                                </button>
                            </form>

                            <!-- Remove -->
                            <form method="post" action="update_cart.php">
                                <input type="hidden" name="medicine_id" value="<?= $item['id']; ?>">
                                <input type="hidden" name="action" value="remove">

                                <button class="btn secondary small-btn" type="submit">
                                    Remove
                                </button>
                            </form>

                        </div>
                    </div>
                </article>

            <?php endforeach; ?>

        </section>

        <p class="total">Total Amount: <?= formatPrice($cartData['total']); ?></p>
        <br>
        <a class="btn" href="checkout.php">Proceed To Checkout</a>

    <?php endif; ?>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>