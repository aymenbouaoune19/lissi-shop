<?php
require __DIR__ . '/../functions.php';
$lang = current_lang();
if (!is_admin()) { header('Location: index.php?lang=' . $lang); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $allowed = ['New', 'Confirmed', 'Shipped', 'Cancelled'];
    $status = $_POST['status'] ?? 'New';
    if (in_array($status, $allowed, true)) db()->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$status, (int) $_POST['id']]);
    header('Location: orders.php?status=' . urlencode($status) . '&lang=' . $lang); exit;
}
require __DIR__ . '/header.php';
$filter = $_GET['status'] ?? 'All';
$allowedFilters = ['All', 'New', 'Confirmed', 'Shipped', 'Cancelled'];
if (!in_array($filter, $allowedFilters, true)) $filter = 'All';
if ($filter === 'All') $orders = db()->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
else { $query = db()->prepare('SELECT * FROM orders WHERE status=? ORDER BY created_at DESC'); $query->execute([$filter]); $orders = $query->fetchAll(); }
$labels = ['All' => $lang === 'ar' ? 'الكل' : 'All', 'New' => $lang === 'ar' ? 'جديد' : 'New', 'Confirmed' => $lang === 'ar' ? 'مؤكد ✓' : 'Confirmed ✓', 'Shipped' => $lang === 'ar' ? 'تم الشحن' : 'Shipped', 'Cancelled' => $lang === 'ar' ? 'ملغى' : 'Cancelled'];
?><div class="dashboard-title"><div><p class="eyebrow"><?= text('orders') ?></p><h2><?= text('orders') ?></h2></div></div><nav class="status-tabs"><?php foreach ($labels as $key => $label): ?><a class="<?= $filter === $key ? 'active' : '' ?>" href="?status=<?= urlencode($key) ?>&lang=<?= $lang ?>"><?= e($label) ?></a><?php endforeach; ?></nav><section class="admin-section"><?php if (!$orders): ?><div class="empty-state"><?= $lang === 'ar' ? 'لا توجد طلبات في هذه الفئة.' : 'No orders in this category.' ?></div><?php endif; ?><?php foreach ($orders as $order): ?><article class="order-card status-<?= strtolower($order['status']) ?>"><div><b>#<?= $order['id'] ?> · <?= e($order['full_name']) ?></b><span><?= e($order['phone']) ?> · <?= e($order['wilaya']) ?> / <?= e($order['commune']) ?></span><span><?= e($order['delivery_method']) ?> · <?= money($order['total']) ?> · <?= e($order['created_at']) ?></span></div><form method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= $order['id'] ?>"><label class="sr-only" for="status-<?= $order['id'] ?>">Status</label><select id="status-<?= $order['id'] ?>" name="status" onchange="this.form.submit()"><option value="New" <?= $order['status'] === 'New' ? 'selected' : '' ?>><?= e($labels['New']) ?></option><option value="Confirmed" <?= $order['status'] === 'Confirmed' ? 'selected' : '' ?>><?= e($labels['Confirmed']) ?></option><option value="Shipped" <?= $order['status'] === 'Shipped' ? 'selected' : '' ?>><?= e($labels['Shipped']) ?></option><option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>><?= e($labels['Cancelled']) ?></option></select></form></article><?php endforeach; ?></section><?php require __DIR__ . '/footer.php'; ?>
