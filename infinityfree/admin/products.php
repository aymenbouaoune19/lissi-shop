<?php
require __DIR__ . '/header.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'create') {
        $imagePath = null;
        if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $mime = mime_content_type($_FILES['image']['tmp_name']);
            if (isset($allowed[$mime]) && $_FILES['image']['size'] <= 4 * 1024 * 1024) {
                $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
                if (!is_dir(__DIR__ . '/../uploads')) mkdir(__DIR__ . '/../uploads', 0755, true);
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/' . $name);
                $imagePath = 'uploads/' . $name;
            }
        }
        $statement = db()->prepare('INSERT INTO products (name, short_name, category, description, price, stock, sizes, color, image_color, image_path, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)');
        $statement->execute([trim($_POST['name']), trim($_POST['short_name']), $_POST['category'], trim($_POST['description']), (float)$_POST['price'], (int)$_POST['stock'], trim($_POST['sizes']), trim($_POST['color']), $_POST['image_color'], $imagePath]);
        $message = $lang === 'ar' ? 'تمت إضافة المنتج بنجاح.' : 'Product added successfully.';
    } elseif (($_POST['action'] ?? '') === 'update') {
        db()->prepare('UPDATE products SET price=?, stock=?, active=? WHERE id=?')->execute([(float)$_POST['price'], (int)$_POST['stock'], isset($_POST['active']) ? 1 : 0, (int)$_POST['id']]);
        $message = $lang === 'ar' ? 'تم حفظ التغييرات.' : 'Changes saved.';
    }
}
?><div class="dashboard-title"><div><p class="eyebrow"><?= text('inventory') ?></p><h2><?= text('inventory') ?></h2></div><button class="dark" type="button" data-product-toggle><?= $lang === 'ar' ? '+ إضافة منتج' : '+ Add product' ?></button></div><?php if ($message): ?><div class="notice"><?= e($message) ?></div><?php endif; ?><section class="product-create" data-product-form><h3><?= $lang === 'ar' ? 'منتج جديد' : 'New product' ?></h3><form class="form" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="create"><div class="form-row"><label>Name<input name="name" required></label><label>Short name<input name="short_name" required></label></div><div class="form-row"><label>Category<select name="category"><option>Women</option><option>Men</option><option>Accessories</option></select></label><label>Color<input name="color" required></label></div><label>Description<textarea name="description" rows="3" required></textarea></label><div class="form-row"><label>Price<input name="price" type="number" min="0" step="0.01" required></label><label>Stock<input name="stock" type="number" min="0" required></label></div><div class="form-row"><label>Sizes<input name="sizes" placeholder="S, M, L" required></label><label>Picture<input name="image" type="file" accept="image/jpeg,image/png,image/webp"></label></div><label>Fallback color<input name="image_color" type="color" value="#e4ebe1"></label><button class="dark" type="submit"><?= $lang === 'ar' ? 'حفظ المنتج' : 'Create product' ?></button></form></section><section class="admin-section"><h3><?= text('inventory') ?></h3><?php foreach (db()->query('SELECT * FROM products ORDER BY id DESC') as $product): ?><form class="line admin-product" method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= $product['id'] ?>"><div class="art" style="background:<?= e($product['image_color']) ?>"><?php if (!empty($product['image_path'])): ?><img src="../<?= e($product['image_path']) ?>" alt="<?= e($product['name']) ?>"><?php else: ?><strong><?= e($product['short_name']) ?></strong><?php endif; ?></div><div><b><?= e($product['name']) ?></b><span>Stock <input name="stock" type="number" min="0" value="<?= $product['stock'] ?>"> Price <input name="price" type="number" min="0" value="<?= $product['price'] ?>"></span></div><label>Active <input name="active" type="checkbox" <?= $product['active'] ? 'checked' : '' ?>></label><button class="dark"><?= text('save') ?></button></form><?php endforeach; ?></section><script>document.querySelector('[data-product-toggle]')?.addEventListener('click',()=>document.querySelector('[data-product-form]')?.classList.toggle('is-visible'));</script><?php require __DIR__ . '/footer.php'; ?>
