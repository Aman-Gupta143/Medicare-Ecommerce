<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Medicines';
$currentPage = 'medicines';

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? 'All Categories');

$rangeStatement = $pdo->query('SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM medicines');
$range = $rangeStatement->fetch();

$lowestPrice = (int) ($range['min_price'] ?? 0);
$highestPrice = (int) ($range['max_price'] ?? 0);

$minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== ''
    ? (int) $_GET['min_price']
    : $lowestPrice;

$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== ''
    ? (int) $_GET['max_price']
    : $highestPrice;

if ($minPrice < $lowestPrice) {
    $minPrice = $lowestPrice;
}

if ($maxPrice > $highestPrice) {
    $maxPrice = $highestPrice;
}

if ($minPrice > $maxPrice) {
    $minPrice = $lowestPrice;
    $maxPrice = $highestPrice;
}

/* Category summary with real stock total */
$categoryStatement = $pdo->query("
    SELECT 
        category,
        COUNT(*) AS item_count,
        SUM(stock) AS total_stock
    FROM medicines
    GROUP BY category
    ORDER BY category
");
$categorySummary = $categoryStatement->fetchAll();

$conditions = ['price BETWEEN ? AND ?'];
$params = [$minPrice, $maxPrice];

if ($search !== '') {
    $conditions[] = 'name LIKE ?';
    $params[] = '%' . $search . '%';
}

if ($category !== '' && $category !== 'All Categories') {
    $conditions[] = 'category = ?';
    $params[] = $category;
}

$sql = "SELECT * FROM medicines";

if ($conditions !== []) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY name";

$statement = $pdo->prepare($sql);
$statement->execute($params);
$medicines = $statement->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<main class="page-shell">

    <!-- Page Heading -->
    <section class="section-head">
        <div>
            <h1>Medicines</h1>
            <p class="price-note">
                Search medicines, select category, and filter price between
                <?= formatPrice((float)$lowestPrice); ?>
                and
                <?= formatPrice((float)$highestPrice); ?>.
            </p>
        </div>
        <div class="muted">Showing <?= count($medicines); ?> results</div>
    </section>

    <!-- Category Cards -->
    <section class="category-grid">
        <?php foreach ($categorySummary as $summary): ?>
            <article class="category-card">
                <h3><?= e($summary['category']); ?></h3>
                <p class="muted">
                    <?= (int)$summary['item_count']; ?> medicines available
                </p>
                <p class="muted">
                    Stock: <?= (int)$summary['total_stock']; ?>
                </p>
            </article>
        <?php endforeach; ?>
    </section>

    <!-- Filter Form -->
    <form class="toolbar" method="get" action="medicines.php">

        <div class="field-group">
            <label for="search">Search Medicine</label>
            <input
                type="text"
                id="search"
                name="search"
                placeholder="Search by medicine name"
                value="<?= e($search); ?>"
            >
        </div>

        <div class="field-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="All Categories">All Categories</option>

                <?php foreach ($categorySummary as $summary): ?>
                    <option
                        value="<?= e($summary['category']); ?>"
                        <?= $category === $summary['category'] ? 'selected' : ''; ?>
                    >
                        <?= e($summary['category']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field-group">
            <label for="min_price">Min Price</label>
            <input
                type="number"
                id="min_price"
                name="min_price"
                min="<?= $lowestPrice; ?>"
                max="<?= $highestPrice; ?>"
                value="<?= $minPrice; ?>"
            >
        </div>

        <div class="field-group">
            <label for="max_price">Max Price</label>
            <input
                type="number"
                id="max_price"
                name="max_price"
                min="<?= $lowestPrice; ?>"
                max="<?= $highestPrice; ?>"
                value="<?= $maxPrice; ?>"
            >
        </div>

        <div class="filter-actions">
            <button class="btn" type="submit">Apply</button>
            <a class="btn secondary" href="medicines.php">Reset</a>
        </div>
    </form>

    <!-- Medicines Grid -->
    <section class="products-grid">

        <?php if ($medicines === []): ?>

            <div class="empty-state">
                No medicines found for your search, category, or price range.
            </div>

        <?php else: ?>

            <?php foreach ($medicines as $medicine): ?>
                <article class="product">

                    <div class="product-top">
                        <div>
                            <strong><?= e($medicine['name']); ?></strong>
                        </div>

                        <span class="category-badge">
                            <?= e($medicine['category']); ?>
                        </span>
                    </div>

                    <p>
                        <?= e($medicine['description']); ?>
                        <br>
                        <strong>Stock: <?= (int)$medicine['stock']; ?></strong>

                        <?php if ((int)$medicine['stock'] <= 0): ?>
                            - <span style="color:red;">Out of Stock</span>
                        <?php endif; ?>
                    </p>

                    <div class="price-row">
                        <span class="price">
                            <?= formatPrice((float)$medicine['price']); ?>
                        </span>

                        <form method="post" action="add_to_cart.php">
                            <input
                                type="hidden"
                                name="medicine_id"
                                value="<?= (int)$medicine['id']; ?>"
                            >

                            <input
                                type="hidden"
                                name="redirect"
                                value="<?= e($_SERVER['REQUEST_URI'] ?? 'medicines.php'); ?>"
                            >

                            <button
                                class="btn"
                                type="submit"
                                <?= (int)$medicine['stock'] <= 0 ? 'disabled' : ''; ?>
                            >
                                <?= (int)$medicine['stock'] <= 0 ? 'Out of Stock' : 'Add To Cart'; ?>
                            </button>
                        </form>
                    </div>

                </article>
            <?php endforeach; ?>

        <?php endif; ?>

    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>