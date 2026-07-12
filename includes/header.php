<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? 'MediCare';
$currentPage = $currentPage ?? '';
$flash = getFlash();
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="brand">
            <h2>MediCare</h2>
            <span>Trusted online pharmacy and prescription support</span>
        </div>

        <nav class="navbar-links">
            <a class="<?= navLinkClass($currentPage, 'home'); ?>" href="index.php">Home</a>
            <a class="<?= navLinkClass($currentPage, 'medicines'); ?>" href="medicines.php">Medicines</a>
            <a class="<?= navLinkClass($currentPage, 'cart'); ?>" href="cart.php">Go To Cart (<?= cartItemCount(); ?>)</a>
            <a class="<?= navLinkClass($currentPage, 'checkout'); ?>" href="checkout.php">Checkout</a>
            <a class="<?= navLinkClass($currentPage, 'upload'); ?>" href="upload.php">Upload</a>
            <a class="<?= navLinkClass($currentPage, 'auth'); ?>" href="auth.php"><?= $user ? 'My Account' : 'Sign In / Sign Up'; ?></a>
            <a class="<?= navLinkClass($currentPage, 'about'); ?>" href="about.php">About</a>
            <a class="<?= navLinkClass($currentPage, 'contact'); ?>" href="contact.php">Contact</a>
            <span class="nav-user"><?= $user ? e($user['name']) : 'Guest User'; ?></span>
            <?php if ($user): ?>
                <a class="btn secondary small-btn" href="logout.php">Logout</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php if ($flash): ?>
        <div class="flash flash-<?= e($flash['type']); ?>">
            <?= e($flash['message']); ?>
        </div>
    <?php endif; ?>