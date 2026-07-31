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
function money($amount): string { global $config; return '<span class="money" dir="ltr">' . number_format((float)$amount, 0, ',', ' ') . ' ' . e($config['currency']) . '</span>'; }
function valid_phone(string $phone): bool { return (bool)preg_match('/^(?:\+213|00213|0)(?:5|6|7)[0-9]{8}$/', preg_replace('/[ .-]/', '', $phone)); }
function cart(): array { return $_SESSION['cart'] ?? []; }
function cart_count(): int { return array_sum(cart()); }
function add_to_cart(int $id, int $quantity = 1): void { $_SESSION['cart'][$id] = min(99, (int)($_SESSION['cart'][$id] ?? 0) + max(1, $quantity)); }
function set_cart_quantity(int $id, int $quantity): void { if ($quantity < 1) remove_from_cart($id); else $_SESSION['cart'][$id] = min(99, $quantity); }
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
function current_lang(): string { $lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'ar'; if (!in_array($lang, ['ar', 'fr', 'en'], true)) $lang = 'ar'; $_SESSION['lang'] = $lang; return $lang; }
function text(string $key): string { $lang = current_lang(); $words = [
    'shop'=>['ar'=>'المتجر','fr'=>'Boutique','en'=>'Shop'], 'women'=>['ar'=>'نساء','fr'=>'Femme','en'=>'Women'], 'men'=>['ar'=>'رجال','fr'=>'Homme','en'=>'Men'], 'accessories'=>['ar'=>'إكسسوارات','fr'=>'Accessoires','en'=>'Accessories'], 'bag'=>['ar'=>'السلة','fr'=>'Panier','en'=>'Bag'], 'all'=>['ar'=>'الكل','fr'=>'Tout','en'=>'All'], 'find'=>['ar'=>'اكتشف اختياراتك اليومية','fr'=>'Trouvez votre quotidien','en'=>'Find your everyday'], 'add'=>['ar'=>'أضف إلى السلة','fr'=>'Ajouter au panier','en'=>'Add to bag'], 'checkout'=>['ar'=>'إتمام الطلب','fr'=>'Commander','en'=>'Checkout'], 'cod'=>['ar'=>'الدفع عند الاستلام','fr'=>'Paiement à la livraison','en'=>'Cash on delivery'], 'details'=>['ar'=>'معلومات التوصيل','fr'=>'Détails de livraison','en'=>'Delivery details'], 'name'=>['ar'=>'الاسم الكامل','fr'=>'Nom complet','en'=>'Full name'], 'phone'=>['ar'=>'رقم الهاتف','fr'=>'Numéro de téléphone','en'=>'Phone number'], 'wilaya'=>['ar'=>'الولاية','fr'=>'Wilaya','en'=>'Wilaya'], 'commune'=>['ar'=>'البلدية','fr'=>'Commune','en'=>'Commune'], 'home'=>['ar'=>'التوصيل إلى المنزل','fr'=>'À domicile','en'=>'Home delivery'], 'bureau'=>['ar'=>'التوصيل إلى المكتب','fr'=>'Bureau','en'=>'Bureau pickup'], 'save'=>['ar'=>'حفظ التغييرات','fr'=>'Enregistrer','en'=>'Save changes'], 'orders'=>['ar'=>'طلبات الزبائن','fr'=>'Commandes clients','en'=>'Customer orders'], 'inventory'=>['ar'=>'المخزون','fr'=>'Stock','en'=>'Inventory'], 'logout'=>['ar'=>'تسجيل الخروج','fr'=>'Déconnexion','en'=>'Log out'], 'login'=>['ar'=>'تسجيل الدخول','fr'=>'Connexion','en'=>'Sign in'], 'admin'=>['ar'=>'لوحة التحكم','fr'=>'Administration','en'=>'Admin'], 'status'=>['ar'=>'الحالة','fr'=>'Statut','en'=>'Status'], 'new'=>['ar'=>'جديد','fr'=>'Nouveau','en'=>'New'], 'confirmed'=>['ar'=>'مؤكد','fr'=>'Confirmé','en'=>'Confirmed'], 'shipped'=>['ar'=>'تم الشحن','fr'=>'Expédié','en'=>'Shipped'], 'cancelled'=>['ar'=>'ملغى','fr'=>'Annulé','en'=>'Cancelled'],
]; return $words[$key][$lang] ?? $words[$key]['en'] ?? $key; }
