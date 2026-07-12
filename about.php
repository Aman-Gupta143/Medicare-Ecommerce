<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'About Us';
$currentPage = 'about';

include __DIR__ . '/includes/header.php';
?>

<main class="page-shell">
    <section class="panel form-panel">
        <h1>About Us</h1>
        <p>MediCare provides genuine medicines at affordable prices with fast delivery, prescription assistance, category-based browsing, and responsive customer support for pharmacy-related needs.</p>
        <br>
        <p class="muted">This PHP version stores medicines, users, orders, prescriptions, and contact messages in MySQL so you can manage the project through phpMyAdmin.</p>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>