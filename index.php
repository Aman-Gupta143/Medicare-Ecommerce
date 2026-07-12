<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'MediCare';
$currentPage = 'home';

$totalMedicineCount = (int) $pdo->query('SELECT COUNT(*) FROM medicines')->fetchColumn();
$totalCategoryCount = (int) $pdo->query('SELECT COUNT(DISTINCT category) FROM medicines')->fetchColumn();
$totalOrderCount = (int) $pdo->query('SELECT COUNT(*) FROM customer_orders')->fetchColumn();

include __DIR__ . '/includes/header.php';
?>

<main class="page-shell">
    <section class="hero">
        <div class="hero-card">
            <h1>Order daily medicines, wellness products, and prescription care in one place.</h1>
            <p>MediCare now stores users, orders, prescriptions, contact messages, and medicines in MySQL so you can manage the project with PHP and phpMyAdmin.</p>

            <div class="hero-actions">
                <a class="btn" href="medicines.php">Shop Medicines</a>
                <a class="btn secondary" href="auth.php">Create Account</a>
            </div>

            <div class="highlights">
                <div class="highlight">
                    <h3><?= $totalMedicineCount; ?> Medicines</h3>
                    <p>Loaded from the `medicines` table instead of hardcoded JavaScript data.</p>
                </div>
                <div class="highlight">
                    <h3>Prescription Upload</h3>
                    <p>Prescription notes and uploaded files are saved into the database with file details.</p>
                </div>
                <div class="highlight">
                    <h3>Database Orders</h3>
                    <p><?= $totalOrderCount; ?> orders currently stored in the `customer_orders` table.</p>
                </div>
            </div>
        </div>

        <aside class="stats-card">
            <h3>Your health essentials, now connected to MySQL.</h3>
            <div class="stats-list">
                <div class="stat-box">
                    <strong><?= $totalMedicineCount; ?></strong>
                    <span>Total listed medicines</span>
                </div>
                <div class="stat-box">
                    <strong><?= $totalCategoryCount; ?></strong>
                    <span>Medicine categories</span>
                </div>
                <div class="stat-box">
                    <strong>24-48 hrs</strong>
                    <span>Agent follow-up after successful order</span>
                </div>
            </div>
        </aside>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>