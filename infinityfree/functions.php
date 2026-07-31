<?php
session_start();
$config = file_exists(__DIR__ . '/config.local.php') ? require __DIR__ . '/config.local.php' : require __DIR__ . '/config.php';

function db(): PDO {
    global $config;
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO(
            "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
            $config['db_user'],
            $config['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}
function money($amount): string { global $config; return number_format((float)$amount, 0, ',', ' ') . ' ' . $config['currency']; }
function cart(): array { return $_SESSION['cart'] ?? []; }
function cart_count(): int { return array_sum(cart()); }
function add_to_cart(int $id, int $quantity = 1): void { $_SESSION['cart'][$id] = min(99, (int)($_SESSION['cart'][$id] ?? 0) + max(1, $quantity)); }
function remove_from_cart(int $id): void { unset($_SESSION['cart'][$id]); }
function cart_products(): array {
    $items = cart(); if (!$items) return [];
    $marks = implode(',', array_fill(0, count($items), '?'));
    $statement = db()->prepare("SELECT * FROM products WHERE id IN ($marks) AND active = 1");
    $statement->execute(array_keys($items));
    $result = [];
    foreach ($statement as $item) { $item['quantity'] = (int)($items[$item['id']] ?? 0); $result[] = $item; }
    return $result;
}
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function check_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) exit('Invalid request.'); }
function is_admin(): bool { return !empty($_SESSION['is_admin']); }
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function require_admin(): void { if (!is_admin()) { header('Location: index.php'); exit; } }
