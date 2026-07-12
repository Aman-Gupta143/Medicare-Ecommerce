<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Please sign in before continuing.');
        redirect('auth.php');
    }
}

function getCart(): array
{
    return $_SESSION['cart'] ?? [];
}

function saveCart(array $cart): void
{
    $_SESSION['cart'] = $cart;
}

function cartItemCount(): int
{
    return array_sum(getCart());
}

function formatPrice(float $amount): string
{
    return 'Rs. ' . number_format($amount, 2);
}

function navLinkClass(string $currentPage, string $page): string
{
    return $currentPage === $page ? 'nav-link active-link' : 'nav-link';
}

function safeRedirectPath(?string $path, string $fallback = 'index.php'): string
{
    if (!$path) {
        return $fallback;
    }

    $path = trim($path);

    if ($path === '' || str_contains($path, '://') || str_starts_with($path, '//')) {
        return $fallback;
    }

    return $path;
}

function fetchMedicinesByIds(PDO $pdo, array $ids): array
{
    if ($ids === []) {
        return [];
    }

    $ids = array_values(array_unique(array_map('intval', $ids)));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $statement = $pdo->prepare("SELECT * FROM medicines WHERE id IN ($placeholders)");
    $statement->execute($ids);

    $medicines = [];

    foreach ($statement->fetchAll() as $medicine) {
        $medicines[(int)$medicine['id']] = $medicine;
    }

    return $medicines;
}

/* =========================
   FIXED CART DETAILS
========================= */
function getCartDetails(PDO $pdo): array
{
    $cart = getCart();

    if ($cart === []) {
        return [
            'items' => [],
            'total' => 0.0,
        ];
    }

    $medicineMap = fetchMedicinesByIds($pdo, array_keys($cart));

    $items = [];
    $total = 0.0;
    $updatedCart = [];

    foreach ($cart as $medicineId => $quantity) {

        $medicineId = (int)$medicineId;
        $quantity = (int)$quantity;

        if (!isset($medicineMap[$medicineId])) {
            continue;
        }

        $medicine = $medicineMap[$medicineId];
        $stock = (int)$medicine['stock'];

        if ($stock <= 0) {
            $quantity = 1; // keep visible but out of stock
        } elseif ($quantity > $stock) {
            $quantity = $stock; // limit cart qty to stock
        }

        if ($quantity <= 0) {
            continue;
        }

        $updatedCart[$medicineId] = $quantity;

        $subtotal = (float)$medicine['price'] * $quantity;
        $total += $subtotal;

        $items[] = [
            'id' => $medicineId,
            'name' => $medicine['name'],
            'category' => $medicine['category'],
            'price' => (float)$medicine['price'],
            'description' => $medicine['description'],
            'stock' => $stock, // ✅ added
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ];
    }

    saveCart($updatedCart);

    return [
        'items' => $items,
        'total' => $total,
    ];
}